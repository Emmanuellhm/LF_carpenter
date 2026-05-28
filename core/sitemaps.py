from django.contrib.sitemaps import Sitemap
from django.urls import reverse
from carpinteros.models import Carpintero

class StaticViewSitemap(Sitemap):
    priority = 0.8
    changefreq = 'weekly'

    def items(self):
        return ['home', 'explorar_carpinteros', 'sobre_nosotros', 'faq', 'contactanos']

    def location(self, item):
        return reverse(item)

class CarpinteroSitemap(Sitemap):
    priority = 0.6
    changefreq = 'daily'

    def items(self):
        # Asumiendo que is_approved indica si el carpintero está activo y visible
        return Carpintero.objects.filter(is_approved=True)

    def location(self, obj):
        return reverse('perfil_publico_carpintero', args=[obj.id])

sitemaps = {
    'static': StaticViewSitemap,
    'carpinteros': CarpinteroSitemap,
}
