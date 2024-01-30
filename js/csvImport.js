$(function() {
    $('#import_button').click(function() {
        let entry_url = $('#entry_url').val();
        let csv = $('input[name="csv"]');

        let formData = new FormData();
        //csvというキー名でcsvファイルをformDataオブジェクトに追加している
        //props('files') → csv要素のfilesプロパティの0番目を選択
        formData.append("csv", csv.prop('files')[0]);
        $.ajax({
            type: "post",
            url : entry_url + "/admin/csvImport.php",
            data : formData,
            contentType: false,
            processData: false,
            dataType   : "html"
        }).then(
            function(data){
                $('#error_message').empty();
                if(data === '1'){
                    $('#head3').html('CSVインポートが完了しました。');
                    $('#close_button').hide();
                    $('#import_form').hide();
                    $reload_button = '<button type="button" class="py-3 w-full bg-gray-800 hover:bg-gray-700 rounded text-white font-bold" onclick="location.reload();">閉じる</button>'
                    $('#reload_button').html($reload_button);
                }else{
                    //PHPから受け取ったJSONを配列に変換
                    let errArr = Object.entries(JSON.parse(data));

                    //エラー内容を表示
                    $('#error_message').addClass("py-2 px-4 md:py-4 px-5 text-red-500");
                    errArr.forEach(element => {
                        Object.entries(element[1]).forEach(err => {
                            $('#error_message').append(err[1] + '<br>');
                            console.log(err[1]);
                        });
                    });
                }
            },
            function(data){

            }
        );
    });

    $('#close_button').click(function() {
        $('#error_message').empty();
        $('#error_message').removeAttr('class');
    });

});
