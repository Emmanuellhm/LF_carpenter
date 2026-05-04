"""
Template tags para paginación
"""
from django import template

register = template.Library()


@register.inclusion_tag('components/pagination.html')
def render_pagination(page_obj, request=None):
    """
    Renderiza el componente de paginación.

    Args:
        page_obj: Page object del paginator
        request: HttpRequest object (opcional, para preservar parámetros GET)

    Returns:
        dict con page_obj y request
    """
    return {
        'page_obj': page_obj,
        'request': request
    }


@register.filter
def get_elided_page_range(page_obj, on_each_side=2, on_ends=1):
    """
    Genera el rango de páginas con elipsis para paginación.

    Args:
        page_obj: Page object del paginator
        on_each_side: Páginas a mostrar a cada lado de la actual
        on_ends: Páginas a mostrar al inicio y final

    Returns:
        Lista de números de página y marcadores de elipsis
    """
    paginator = page_obj.paginator
    current = page_obj.number
    total = paginator.num_pages

    pages = []

    # Agregar páginas del inicio
    if on_ends and current > on_ends + on_each_side + 1:
        pages.extend(range(1, on_ends + 1))
        if on_ends + 1 < current - on_each_side:
            pages.append('...')
    else:
        pages.extend(range(1, current))

    # Agregar páginas alrededor de la actual
    start = max(1, current - on_each_side)
    end = min(total + 1, current + on_each_side + 1)
    pages.extend(range(start, end))

    # Agregar páginas del final
    if on_ends and current < total - on_each_side - on_ends:
        if end <= total - on_ends:
            pages.append('...')
        pages.extend(range(total - on_ends + 1, total + 1))
    else:
        pages.extend(range(end, total + 1))

    # Eliminar duplicados manteniendo el orden
    seen = set()
    unique_pages = []
    for page in pages:
        if page not in seen:
            seen.add(page)
            unique_pages.append(page)

    return unique_pages