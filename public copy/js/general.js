$('.favorito').on('click',function(e){

    e.preventDefault();
    var $this = $(this),
        url= $this.data('url'),
        idMarcador=$this.data('id');

    $this.addClass('disabled');
    
    $.post(url, {id: idMarcador})
        .done(function(respuesta){
            if(respuesta.actualizado){
                $this.toggleClass('activo');
            }
            $this.removeClass('disabled');
        })
        .fail(function(){
            $this.removeClass('disabled');
        });

});