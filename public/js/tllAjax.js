// noinspection JSUnusedGlobalSymbols
const tllAjax = {
    showStorages: () => {
        $('#div-form').hide();
        $('#div-details').html('');
        tllAjax.post('/storage/list', {}, '#div-list');

        return false;
    },

    showStorageDetails: (id) => {
        tllAjax.post('/storage/details', {'id': id}, '#div-details');

        return false;
    },

    newStorage: () => {
        $('#div-form').show();
        tllAjax.post('/storage/new', {}, '#div-form');

        return false;
    },

    save: (form, entity, options) => {
        if (options === undefined) {
            options = {};
        }

        const targetSelector = options.targetSelector === undefined ? '#div-details' : options.targetSelector;

        let data = JSON.stringify($(form).serializeArray() );
        tllAjax.post(entity + '/save', {'data': data}, targetSelector);

        return false;
    },

    doAddProduct: (form) => {
        let data = JSON.stringify($(form).serializeArray() );
        tllAjax.post('/storage/do-add-product', {'data': data}, '#div-details');

        return false;
    },

    addProduct: () => {
        $('#div-form').show();
        tllAjax.post('/storage/add-product', {}, '#div-form');

        return false;
    },

    escape: (hideSelector, showSelector) => {
        $(hideSelector).hide();
        if (showSelector) {
            $(showSelector).show();
        }

        return false;
    },

    post: (url, data, targetSelector) => {
        $.post(url,
            data,
            function (response) {
                if (targetSelector !== 'nothing') {
                    $(targetSelector).html(response);
                    $('.focus-here').focus();

                    return 1;
                }
            }
        );
    },

    defaultInput: () => {
        $('#name').val('Teljesen új raktár');
        $('#postalCode').val('8900');
        $('#city').val('Zalaegerszeg');
        $('#street').val('Újlaki krt. 123.');
    }
}
