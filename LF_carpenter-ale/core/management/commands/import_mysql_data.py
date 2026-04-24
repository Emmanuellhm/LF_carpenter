"""
Management command to import data from the old MySQL dump into Django PostgreSQL.
Maps the old MySQL schema to the new Django models.
"""
import re
from datetime import datetime
from decimal import Decimal
from django.core.management.base import BaseCommand
from django.db import transaction
from core.models import User
from carpinteros.models import Carpintero, Portafolio, Comentario, Review
from proyectos.models import SolicitudProyecto, Material
from contrataciones.models import (
    Notificacion, ActivityLog, FailedLogin, Traceability,
    UserPreference
)


class Command(BaseCommand):
    help = 'Import data from old MySQL dump into Django PostgreSQL'

    def add_arguments(self, parser):
        parser.add_argument(
            '--sql-file',
            type=str,
            default='lf_uptade(1).sql',
            help='Path to the SQL dump file'
        )

    def handle(self, *args, **options):
        sql_file = options['sql_file']
        self.stdout.write(self.style.WARNING(f'Reading SQL file: {sql_file}'))

        try:
            with open(sql_file, 'r', encoding='utf-8') as f:
                sql_content = f.read()
        except FileNotFoundError:
            self.stdout.write(self.style.ERROR(f'File not found: {sql_file}'))
            return

        with transaction.atomic():
            # Parse and import in order (respecting foreign key dependencies)
            self._import_users(sql_content)
            self._import_carpenters(sql_content)
            self._import_portfolio(sql_content)
            self._import_comments(sql_content)
            self._import_notifications(sql_content)
            self._import_project_requests(sql_content)
            self._import_activity_logs(sql_content)
            self._import_failed_logins(sql_content)
            self._import_traceability(sql_content)
            self._import_user_preferences(sql_content)

        self.stdout.write(self.style.SUCCESS('✅ Data import completed successfully!'))
        self._print_summary()

    def _parse_inserts(self, sql_content, table_name):
        """Parse INSERT statements for a given table and return list of value tuples."""
        # Find all INSERT INTO statements for this table
        pattern = rf"INSERT INTO `{table_name}`\s*\(([^)]+)\)\s*VALUES\s*"
        matches = re.finditer(pattern, sql_content)

        results = []
        for match in matches:
            columns = [c.strip().strip('`') for c in match.group(1).split(',')]
            # Get the values portion after VALUES
            start = match.end()
            # Find all value tuples until the semicolon
            values_str = sql_content[start:]
            # Find the end of this INSERT statement
            depth = 0
            i = 0
            rows = []
            current_row = []
            in_string = False
            escape_next = False
            current_value = ''

            while i < len(values_str):
                ch = values_str[i]

                if escape_next:
                    current_value += ch
                    escape_next = False
                    i += 1
                    continue

                if ch == '\\':
                    escape_next = True
                    current_value += ch
                    i += 1
                    continue

                if ch == "'" and not escape_next:
                    in_string = not in_string
                    current_value += ch
                    i += 1
                    continue

                if in_string:
                    current_value += ch
                    i += 1
                    continue

                if ch == '(':
                    depth += 1
                    if depth == 1:
                        current_value = ''
                        current_row = []
                    i += 1
                    continue

                if ch == ')':
                    depth -= 1
                    if depth == 0:
                        # End of a row
                        current_row.append(current_value.strip())
                        rows.append(current_row)
                        current_value = ''
                        current_row = []
                    i += 1
                    continue

                if ch == ',' and depth == 1:
                    current_row.append(current_value.strip())
                    current_value = ''
                    i += 1
                    continue

                if ch == ';' and depth == 0:
                    break

                if depth >= 1:
                    current_value += ch

                i += 1

            # Convert rows to dicts
            for row in rows:
                row_dict = {}
                for idx, col in enumerate(columns):
                    if idx < len(row):
                        val = row[idx]
                        # Clean up value
                        if val == 'NULL':
                            val = None
                        elif val.startswith("'") and val.endswith("'"):
                            val = val[1:-1]
                            # Unescape
                            val = val.replace("\\'", "'")
                            val = val.replace('\\"', '"')
                            val = val.replace('\\n', '\n')
                            val = val.replace('\\r', '\r')
                            val = val.replace('\\\\', '\\')
                        row_dict[col] = val
                    else:
                        row_dict[col] = None
                results.append(row_dict)

        return results

    def _safe_int(self, val, default=0):
        if val is None:
            return default
        try:
            return int(val)
        except (ValueError, TypeError):
            return default

    def _safe_decimal(self, val, default=None):
        if val is None:
            return default
        try:
            return Decimal(val)
        except Exception:
            return default

    def _safe_datetime(self, val):
        if val is None:
            return None
        try:
            return datetime.strptime(val, '%Y-%m-%d %H:%M:%S')
        except Exception:
            return None

    def _safe_bool(self, val):
        if val is None:
            return False
        try:
            return int(val) == 1
        except (ValueError, TypeError):
            return False

    # ----- USER IMPORT -----
    def _import_users(self, sql_content):
        self.stdout.write('Importing users...')
        rows = self._parse_inserts(sql_content, 'users')
        count = 0
        # Keep track of old user_id -> new User for FK mapping
        self.user_map = {}

        for row in rows:
            old_id = self._safe_int(row.get('user_id'))
            email = row.get('email', '')
            full_name = row.get('full_name', '')
            phone = row.get('phone', '')
            role = row.get('role', 'user')
            is_active = self._safe_bool(row.get('is_active', '1'))
            city = row.get('city', '') or ''

            if not email:
                continue

            # Skip if already exists
            if User.objects.filter(email=email).exists():
                user = User.objects.get(email=email)
                self.user_map[old_id] = user
                continue

            user = User(
                email=email,
                username=email,
                full_name=full_name,
                phone=phone or '',
                city=city,
                role=role,
                is_active=is_active,
            )
            user.set_password('LFcarpinter2025')
            user.save()
            self.user_map[old_id] = user
            count += 1

        self.stdout.write(self.style.SUCCESS(f'  → {count} users imported'))

    # ----- CARPENTER IMPORT -----
    def _import_carpenters(self, sql_content):
        self.stdout.write('Importing carpenters...')
        rows = self._parse_inserts(sql_content, 'carpenters')
        count = 0
        self.carpenter_map = {}  # old carpenter_id -> new Carpintero

        for row in rows:
            old_carp_id = self._safe_int(row.get('carpenter_id'))
            old_user_id = self._safe_int(row.get('user_id'))
            carpenter_name = row.get('carpenter_name', '')
            email = row.get('email', '')
            description = row.get('description', '') or ''
            specialties = row.get('specialties', '') or ''
            experience = self._safe_int(row.get('experience_years'))
            approved_val = self._safe_int(row.get('approved'))
            is_verified = self._safe_bool(row.get('is_verified'))
            rating_avg = self._safe_decimal(row.get('rating_avg'))
            budget_range = self._safe_decimal(row.get('budget_range'))
            cv_file = row.get('cv_file', '') or ''

            # Find linked user
            user = self.user_map.get(old_user_id)

            if not user:
                # Create user from carpenter data if not exists
                if carpenter_name and email:
                    if User.objects.filter(email=email).exists():
                        user = User.objects.get(email=email)
                    else:
                        user = User(
                            email=email if email else f'carpenter_{old_carp_id}@lf.com',
                            username=email if email else f'carpenter_{old_carp_id}@lf.com',
                            full_name=carpenter_name,
                            role='carpenter',
                        )
                        user.set_password('LFcarpinter2025')
                        user.save()
                elif carpenter_name:
                    temp_email = f'carpenter_{old_carp_id}@lf.com'
                    if User.objects.filter(email=temp_email).exists():
                        user = User.objects.get(email=temp_email)
                    else:
                        user = User(
                            email=temp_email,
                            username=temp_email,
                            full_name=carpenter_name or f'Carpintero {old_carp_id}',
                            role='carpenter',
                        )
                        user.set_password('LFcarpinter2025')
                        user.save()
                else:
                    continue

            # Skip if carpenter profile already exists for this user
            if Carpintero.objects.filter(user=user).exists():
                self.carpenter_map[old_carp_id] = Carpintero.objects.get(user=user)
                continue

            # Ensure user has carpenter role
            if user.role != 'carpenter':
                user.role = 'carpenter'
                user.save()

            carp = Carpintero(
                user=user,
                specialties=specialties,
                experience_years=experience,
                description=description,
                hoja_vida=cv_file if cv_file else '',
                is_verified=is_verified,
                approved=(approved_val == 1),
                rating_avg=rating_avg,
                budget_range=budget_range,
            )
            carp.save()
            self.carpenter_map[old_carp_id] = carp
            count += 1

        self.stdout.write(self.style.SUCCESS(f'  → {count} carpenters imported'))

    # ----- PORTFOLIO IMPORT -----
    def _import_portfolio(self, sql_content):
        self.stdout.write('Importing portfolio projects...')
        rows = self._parse_inserts(sql_content, 'portafolio')
        count = 0
        self.portfolio_map = {}  # old project_id -> new Portafolio

        for row in rows:
            old_proj_id = self._safe_int(row.get('project_id'))
            carpenter_user_id = self._safe_int(row.get('carpenter_user_id'))
            title = row.get('title', '') or ''
            description = row.get('description', '') or ''
            image_path = row.get('image_path', '') or ''
            price = self._safe_decimal(row.get('price'), Decimal('0'))

            # Find carpenter by their user_id from old system
            carpenter = None
            user = self.user_map.get(carpenter_user_id)
            if user:
                try:
                    carpenter = Carpintero.objects.get(user=user)
                except Carpintero.DoesNotExist:
                    pass

            if not carpenter:
                # Try by carpenter_id
                carpenter = self.carpenter_map.get(carpenter_user_id)

            if not carpenter:
                continue

            port = Portafolio(
                carpenter=carpenter,
                title=title,
                description=description,
                image=image_path,
                price=price,
            )
            port.save()
            self.portfolio_map[old_proj_id] = port
            count += 1

        self.stdout.write(self.style.SUCCESS(f'  → {count} portfolio projects imported'))

    # ----- COMMENTS IMPORT -----
    def _import_comments(self, sql_content):
        self.stdout.write('Importing comments...')
        rows = self._parse_inserts(sql_content, 'project_comments')
        count = 0

        for row in rows:
            old_proj_id = self._safe_int(row.get('project_id'))
            old_user_id = self._safe_int(row.get('user_id'))
            comment_text = row.get('comment', '') or ''

            portfolio = self.portfolio_map.get(old_proj_id)
            user = self.user_map.get(old_user_id)

            if not portfolio:
                continue

            Comentario(
                proyecto=portfolio,
                user=user,
                comment=comment_text,
                rating=5,
            ).save()
            count += 1

        self.stdout.write(self.style.SUCCESS(f'  → {count} comments imported'))

    # ----- NOTIFICATIONS IMPORT -----
    def _import_notifications(self, sql_content):
        self.stdout.write('Importing notifications...')
        rows = self._parse_inserts(sql_content, 'notifications')
        count = 0

        for row in rows:
            old_user_id = self._safe_int(row.get('user_id'))
            message = row.get('message', '') or ''
            is_read = self._safe_bool(row.get('is_read'))

            user = self.user_map.get(old_user_id)
            if not user:
                continue

            Notificacion(
                user=user,
                message=message,
                is_read=is_read,
            ).save()
            count += 1

        self.stdout.write(self.style.SUCCESS(f'  → {count} notifications imported'))

    # ----- PROJECT REQUESTS IMPORT -----
    def _import_project_requests(self, sql_content):
        self.stdout.write('Importing project requests...')
        rows = self._parse_inserts(sql_content, 'project_requests')
        count = 0

        for row in rows:
            old_user_id = self._safe_int(row.get('user_id'))
            carpenter_user_id = self._safe_int(row.get('carpenter_user_id'))
            description_text = row.get('project_description', '') or ''
            contact = row.get('contact_info', '') or ''
            status = row.get('status', 'pending') or 'pending'

            user = self.user_map.get(old_user_id)
            if not user:
                self.stdout.write(f"  - Skipped: User {old_user_id} not found in map")
                continue

            # Find carpenter by user_id
            carpenter = None
            carpenter_user = self.user_map.get(carpenter_user_id)
            if carpenter_user:
                try:
                    carpenter = Carpintero.objects.get(user=carpenter_user)
                except Carpintero.DoesNotExist:
                    pass

            if not carpenter:
                # Try finding by old carpenter_id map just in case
                carpenter = self.carpenter_map.get(carpenter_user_id)
            
            if not carpenter:
                self.stdout.write(f"  - Skipped: Carpenter for user_id {carpenter_user_id} not found")
                continue

            SolicitudProyecto(
                user=user,
                carpenter=carpenter,
                title=description_text[:100] if description_text else 'Solicitud de proyecto',
                description=description_text,
                contact_info=contact,
                status=status,
            ).save()
            count += 1

        self.stdout.write(self.style.SUCCESS(f'  → {count} project requests imported'))

    # ----- ACTIVITY LOGS IMPORT -----
    def _import_activity_logs(self, sql_content):
        self.stdout.write('Importing activity logs...')
        rows = self._parse_inserts(sql_content, 'activity_logs')
        count = 0

        for row in rows:
            old_user_id = self._safe_int(row.get('user_id'))
            action_type = row.get('action_type', '') or ''
            description = row.get('description', '') or ''
            ip_address = row.get('ip_address', '') or ''
            user_agent = row.get('user_agent', '') or ''

            user = self.user_map.get(old_user_id)

            ActivityLog(
                user=user,
                action_type=action_type,
                description=description,
                ip_address=ip_address if ip_address else None,
                user_agent=user_agent,
            ).save()
            count += 1

        self.stdout.write(self.style.SUCCESS(f'  → {count} activity logs imported'))

    # ----- FAILED LOGINS IMPORT -----
    def _import_failed_logins(self, sql_content):
        self.stdout.write('Importing failed logins...')
        rows = self._parse_inserts(sql_content, 'failed_logins')
        count = 0

        for row in rows:
            old_user_id = self._safe_int(row.get('user_id'))
            email_attempted = row.get('email_attempted', '') or ''
            ip_address = row.get('ip_address', '') or ''
            user_agent = row.get('user_agent', '') or ''
            fail_reason = row.get('fail_reason', '') or ''

            user = self.user_map.get(old_user_id)

            FailedLogin(
                user=user,
                email_attempted=email_attempted,
                ip_address=ip_address if ip_address else None,
                user_agent=user_agent,
                fail_reason=fail_reason,
            ).save()
            count += 1

        self.stdout.write(self.style.SUCCESS(f'  → {count} failed logins imported'))

    # ----- TRACEABILITY IMPORT -----
    def _import_traceability(self, sql_content):
        self.stdout.write('Importing traceability...')
        rows = self._parse_inserts(sql_content, 'traceability')
        count = 0

        for row in rows:
            action_type = row.get('action_type', '') or ''
            performed_by_id = self._safe_int(row.get('performed_by'))
            affected_user_id = self._safe_int(row.get('affected_user'))
            affected_table = row.get('affected_table', '') or ''
            affected_id = self._safe_int(row.get('affected_id'))
            old_value = row.get('old_value', '') or ''
            new_value = row.get('new_value', '') or ''
            authority_level = row.get('authority_level', 'user') or 'user'

            performed_by = self.user_map.get(performed_by_id)
            affected_user = self.user_map.get(affected_user_id)

            if not performed_by:
                continue

            Traceability(
                action_type=action_type,
                performed_by=performed_by,
                affected_user=affected_user,
                affected_table=affected_table,
                affected_id=affected_id if affected_id else None,
                old_value=old_value,
                new_value=new_value,
                authority_level=authority_level,
            ).save()
            count += 1

        self.stdout.write(self.style.SUCCESS(f'  → {count} traceability records imported'))

    # ----- USER PREFERENCES IMPORT -----
    def _import_user_preferences(self, sql_content):
        self.stdout.write('Importing user preferences...')
        rows = self._parse_inserts(sql_content, 'user_preferences')
        count = 0

        for row in rows:
            old_user_id = self._safe_int(row.get('user_id'))
            materials = row.get('preferred_materials', '') or ''
            styles = row.get('preferred_styles', '') or ''
            notif = self._safe_bool(row.get('notifications_enabled', '1'))

            user = self.user_map.get(old_user_id)
            if not user:
                continue

            # Skip if preference already exists
            if UserPreference.objects.filter(user=user).exists():
                continue

            UserPreference(
                user=user,
                preferred_materials=materials,
                preferred_styles=styles,
                notifications_enabled=notif,
            ).save()
            count += 1

        self.stdout.write(self.style.SUCCESS(f'  → {count} user preferences imported'))

    def _print_summary(self):
        self.stdout.write('\n' + '=' * 50)
        self.stdout.write('IMPORT SUMMARY')
        self.stdout.write('=' * 50)
        self.stdout.write(f'  Users:              {User.objects.count()}')
        self.stdout.write(f'  Carpenters:         {Carpintero.objects.count()}')
        self.stdout.write(f'  Portfolio items:    {Portafolio.objects.count()}')
        self.stdout.write(f'  Comments:           {Comentario.objects.count()}')
        self.stdout.write(f'  Notifications:      {Notificacion.objects.count()}')
        self.stdout.write(f'  Project requests:   {SolicitudProyecto.objects.count()}')
        self.stdout.write(f'  Activity logs:      {ActivityLog.objects.count()}')
        self.stdout.write(f'  Failed logins:      {FailedLogin.objects.count()}')
        self.stdout.write(f'  Traceability:       {Traceability.objects.count()}')
        self.stdout.write(f'  User preferences:   {UserPreference.objects.count()}')
        self.stdout.write('=' * 50)
        self.stdout.write(self.style.WARNING(
            '\n⚠️  All imported users have temporary password: LFcarpinter2025'
        ))
        self.stdout.write(self.style.WARNING(
            '   Please create a superuser with: python manage.py createsuperuser'
        ))
