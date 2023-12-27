$(function() {
    $('#participation_button').click(function() {

      let event_id = $('#event_id').val();
      let entry_url = $('#entry_url').val();

      $.ajax({
          type: "get",
          url : entry_url + "/modal.php?event_id=" + encodeURIComponent(event_id),
      }).then(
          function(data){
                console.log('a');
              updateModalButton(data)
          },
          function(data){
          },
      );
    });

    $('#cancel_button').click(function() {

      let event_id = $('#event_id').val();
      let entry_url = $('#entry_url').val();

      $.ajax({
          type: "get",
          url : entry_url + "/modal.php?event_id=" + encodeURIComponent(event_id),
      }).then(
          function(data){
              updateModalButton(data)
          },
          function(data){
          },
      );
    });


    function updateModalButton(event_id){

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

        console.log('a');

    }
});
