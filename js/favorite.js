$(document).ready(function() {
    $('#favorite_button').click(function(){

        let event_id = $('#event_id').val();
        let entry_url = $('#entry_url').val();

        $.ajax({
            type : "get",
            url : entry_url + "loginCheck.php",
        }).then(
            function(data){
                json = JSON.parse(data);
                let is_login = json.is_login;
                if(is_login){
                    pushFavorite();
                }
            },
            function(data){
            },
        );

        function pushFavorite(){
            $.ajax({
                type : "get",
                url : entry_url + "/favorite.php?event_id=" + encodeURIComponent(event_id),
            }).then(
                function(data){
                    if(data === 'no' || data === ''){
                        alert('不正なリクエスト');
                    }else{
                        json = JSON.parse(data);
                        isFavoriteUpdated = json.isFavoriteUpdated;
                        favorites = json.favorites;

                        if(isFavoriteUpdated === 0){
                            $('#favorite_icon').removeClass('fa-solid').addClass('fa-regular').css('color', '#d6d6d6');
                        }else{
                            $('#favorite_icon').removeClass('fa-regular').addClass('fa-solid').css('color', '#ff388e');
                        }

                        if(favorites === 0){
                            $('#favorites').html("&nbsp;");
                        }else{
                            $('#favorites').html(favorites);
                        }
                        console.log(json);
                    }
                },
                function(data){
                    alert("読み込み失敗");
                },
            );
        };
    });
});
