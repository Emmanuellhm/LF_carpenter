"""
Management command to reset all user passwords.
"""
from django.core.management.base import BaseCommand
from core.models import User


class Command(BaseCommand):
    help = 'Reset all user passwords to a default value'

    def add_arguments(self, parser):
        parser.add_argument(
            '--password',
            type=str,
            default='LFcarpinter2025',
            help='New password for all users'
        )

    def handle(self, *args, **options):
        password = options['password']
        users = User.objects.all()
        count = 0
        for u in users:
            u.set_password(password)
            u.save(update_fields=['password'])
            count += 1
            self.stdout.write(f'  Reset: {u.email}')
        
        self.stdout.write(self.style.SUCCESS(f'\n✅ {count} passwords reset to: {password}'))
