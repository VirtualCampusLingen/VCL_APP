function setFlash(type, msg){
  switch (type){
    case 'success':
      $(".flash").removeClass("flash_error");
      $(".flash").removeClass("flash_notmodified");
      $(".flash").addClass("flash_success");
      break;
    case 'notmodified':
      $(".flash").removeClass("flash_error");
      $(".flash").removeClass("flash_success");
      $(".flash").addClass("flash_notmodified");
      break;
    case 'error':
      $(".flash").removeClass("flash_success");
      $(".flash").removeClass("flash_notmodified");
      $(".flash").addClass("flash_error");
      break;
  }
  $(".flash_msg").html(msg);
  $(".flash").show();
}

jQuery.fn.extend({
  appendPartial: function(name, callback){
    var element = this;
    $.get("_"+name, function(data){
      $(element).append(data);
      if(typeof callback != 'undefined') callback();
    })
  }
});