$(function() {
    $('#regist').submit(function(event){
        event.preventDefault();
        let entry_url = $('#entry_url').val();

        let formData = $(this).serializeArray();
        console.log(formData);
        $.ajax({
            type : "post",
            url : entry_url + "/participation.php",
            data : formData,
        }).then(
            function(data){
                if(data === 'no' || data === ''){
                    alert('不正なリクエスト');
                }else{
                    event_id = formData[1]['value'];
                    updateModalButton(entry_url, event_id);
                }
            },
            function(data){
                alert("読み込み失敗");
            },
        );
    });

    $('#cancel').submit(function(event){
        event.preventDefault();
        let entry_url = $('#entry_url').val();

        let formData = $(this).serializeArray();
        $.ajax({
            type : "post",
            url : entry_url + "/participation.php",
            data :formData,
        }).then(
            function(data){
                if(data === 'no' || data === ''){
                    alert('不正なリクエスト');
                }else{
                    event_id = formData[1]['value'];
                    updateModalButton(entry_url, event_id);
                }
            },
            function(data){
                alert("読み込み失敗");
            },
        );
    });

    function updateModalButton(entry_url, event_id){
        $.ajax({
            type: "get",
            url : entry_url + "/modal.php?event_id=" + encodeURIComponent(event_id),
        }).then(
            function(data){
                if (data !== '99' && data !== '') {
                    $('#modal_button').html('参加中');
                    // $('#event_participants').show();

                } else {
                    $('#modal_button').html('参加する');
                    // $('#event_participants').hide();
                }
            },
            function(data){
            },
        );

    }
});
