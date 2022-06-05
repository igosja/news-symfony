jQuery(document).ready(function () {
    $('.filters').on('change', 'input, select', function () {
        let filtersDiv = $('.filters');
        let url = new URL(filtersDiv.data('url'));
        let params = filtersDiv.find('input, select').serializeArray();
        for (let i=0; i<params.length; i++) {
            url.searchParams.append(params[i].name, params[i].value);
        }
        location.href = url.href;
    });
});