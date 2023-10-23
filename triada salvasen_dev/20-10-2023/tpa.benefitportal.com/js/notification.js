$(document).on("click", "#notification_cont", function() {
  $(this).fadeOut('slow');
});

function setNotifySuccess(msg) {
  setNotify('success', msg);
}

function setNotifyError(msg) {
  setNotify('danger', msg);
}

function setNotifyWarning(msg) {
  setNotify('warning', msg);
}

function setNotifyInfo(msg) {
  setNotify('info', msg);
}

function setNotify(msg_class, msg) {
  $('#notification_msg_cont').html(msg);
  if(msg_class == 'success'){
    $('#notification_cont').find('img').attr('src',$HOST + '/images/card_right.svg');
  }else{
    $('#notification_cont').find('img').attr('src',$HOST + '/images/card_close.svg');
  }
  $('#notification_cont').removeClass('success danger alert-success alert-danger alert-warning alert-info')
    .addClass(msg_class).fadeIn();

  setTimeout(function() {
    $('#notification_cont:visible').fadeOut();
  }, 3000)
}

function destroyNotify() {
  $('#notification_cont')
    .delay(3000)
    .fadeOut('slow', function() {
      $(this).removeClass('success danger alert-success alert-danger alert-warning alert-info');
    });
}
