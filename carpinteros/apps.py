from django.apps import AppConfig


class CarpinterosConfig(AppConfig):
    name = 'carpinteros'

    def ready(self):
        import carpinteros.signals
