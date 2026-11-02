
$(document).ready(function () {

    // Seleciona todos os cards
    $('.module-card').on('click', function (event) {

        // Não interfere nos links e botões internos
        if ($(event.target).closest('a, button').length) {
            return;
        }

        // Obtém a URL armazenada no atributo data-href
        const url = $(this).data('href');

        // Redireciona para a página
        window.location.href = url;
    });

    // Navegação pelo teclado
    $('.module-card').on('keydown', function (event) {

        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();

            window.location.href = $(this).data('href');
        }

    });

});
