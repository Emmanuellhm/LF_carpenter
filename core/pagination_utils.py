"""
Utilidades de Paginación para LF Carpinter
Sistema de paginación profesional con Tailwind CSS
"""

from django.core.paginator import Paginator, EmptyPage, PageNotAnInteger
from django.shortcuts import render


class PaginationMixin:
    """
    Mixin para agregar paginación a vistas basadas en funciones.

    Uso:
        def mi_vista(request):
            queryset = MiModelo.objects.all()
            paginator = PaginationMixin()
            page_obj = paginator.paginate(queryset, request, per_page=12)
            return render(request, 'template.html', {'page_obj': page_obj})
    """

    def paginate(self, queryset, request, per_page=12, param_name='page'):
        """
        Pagina un queryset.

        Args:
            queryset: Queryset a paginar
            request: HttpRequest object
            per_page: Número de items por página (default: 12)
            param_name: Nombre del parámetro de página en la URL

        Returns:
            Page object con los items de la página actual
        """
        paginator = Paginator(queryset, per_page)
        page = request.GET.get(param_name, 1)

        try:
            page_obj = paginator.page(page)
        except PageNotAnInteger:
            page_obj = paginator.page(1)
        except EmptyPage:
            page_obj = paginator.page(paginator.num_pages)

        return page_obj


def paginate_queryset(queryset, request, per_page=12, param_name='page'):
    """
    Función helper para paginar un queryset.

    Args:
        queryset: Queryset a paginar
        request: HttpRequest object
        per_page: Número de items por página (default: 12)
        param_name: Nombre del parámetro de página en la URL

    Returns:
        Page object con los items de la página actual
    """
    paginator = Paginator(queryset, per_page)
    page = request.GET.get(param_name, 1)

    try:
        page_obj = paginator.page(page)
    except PageNotAnInteger:
        page_obj = paginator.page(1)
    except EmptyPage:
        page_obj = paginator.page(paginator.num_pages)

    return page_obj


def get_page_range(page_obj, window=2):
    """
    Genera el rango de páginas para mostrar en la paginación.

    Args:
        page_obj: Page object del paginator
        window: Número de páginas a mostrar a cada lado (default: 2)

    Returns:
        Lista de números de página a mostrar
    """
    current = page_obj.number
    total = page_obj.paginator.num_pages

    start = max(1, current - window)
    end = min(total, current + window)

    pages = list(range(start, end + 1))

    # Agregar primera página si no está incluida
    if pages and pages[0] > 1:
        if pages[0] > 2:
            pages = [1, '...'] + pages
        else:
            pages = [1] + pages

    # Agregar última página si no está incluida
    if pages and pages[-1] < total:
        if pages[-1] < total - 1:
            pages = pages + ['...', total]
        else:
            pages = pages + [total]

    return pages