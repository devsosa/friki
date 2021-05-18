function MeGusta(id){

    //alert(id);
    const ruta = Routing.generate('Likes');
    
    $.ajax({
        type: "POST",
        url: ruta,
        data: ({id:id}),
        async: true,
        dataType: "json",
        success: function (data) {
            //console.log(data['likes']);
            window.location.reload();
        }
    });
}