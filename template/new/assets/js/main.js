$(function () {

    /* 1. Remove todo listener de submit que o template possa ter colocado */
    $('.contact-action').off('submit');
  
    /* 2. Agora sim, o seu handler */
    $('.contact-action').on('submit', function (e) {
      e.preventDefault();
  
      const $form   = $(this);
      const $btn    = $form.find('button[type="submit"]');
      const $msgBox = $('#message');
  
      $btn.prop('disabled', true);
  
      $.ajax({
        url : 'index.php?action=depoimento',
        type: 'POST',
        data: $form.serialize(),
        dataType: 'json',
  
        /*success(resp) {
          $form.trigger('reset');
          $msgBox
            .html(resp.success
                    ? '<h5 class="color-primary">Depoimento nº ${resp.id} enviado com sucesso!</h5>'
                    : '<h5 class="text-danger">Erro ao enviar.</h5>')
            .slideDown();
        }*/
            success(resp){
                if (resp.success){
                   $('#message').html(
                      `<h5 class="color-primary">Depoimento de ${resp.data.nome} salvo com sucesso!</h5>`
                   ).slideDown();
                }else{
                   $('#message').html(
                      `<h5 class="text-danger">${resp.msg||'Erro desconhecido'}</h5>`
                   ).slideDown();
                }
              },
  
        error(xhr, status, err) {
          $msgBox.html(
            `<h5 class="text-danger">Falha: ${err}</h5>`
          ).slideDown();
        },
  
        complete() {
          setTimeout(() => $msgBox.slideUp(), 6000);
          $btn.prop('disabled', false);
        }
      });
    });
  
  });
  