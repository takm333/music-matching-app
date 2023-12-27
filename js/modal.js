$(function() {
    $('#modal_button').click(function() {

    let event_id = $('#event_id').val();
    let entry_url = $('#entry_url').val();

    $.ajax({
        type: "get",
        url : entry_url + "/modal.php?event_id=" + encodeURIComponent(event_id),
    }).then(
        function(data){
            updateModalContent(data)
        },
        function(data){
        },
    );
    });

    // モーダルの内容を更新する関数
    function updateModalContent(data)
    {
      let cancel_button = $('#cancel_button');
      if (data !== '99' && data !== '') {
        cancel_button.show();
        status_id = '#' +  data;
        $(status_id).prop('checked', true);
        $('#participation_button').html('状態を変更する');
      } else {
        cancel_button.hide();
        $(status_id).prop('checked', false);
        $('#participation_button').html('状態を登録して参加する');
      }
    }

});
