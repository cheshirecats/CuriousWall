$(document).ready(function(){
//======================================================================== general

function hover_a() {
  $(this).addClass('current');
  $(this).find('i').addClass('current');
}
function hover_b() {
  $(this).removeClass('current');
  $(this).find('i').removeClass('current');
}
$('.button').hover(hover_a, hover_b);
$('.hover').hover(hover_a, hover_b);

var $_GET = {};
document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
  function decode(s) {
    return decodeURIComponent(s.split("+").join(" "));
  }
  $_GET[decode(arguments[1])] = decode(arguments[2]);
});

function isINT(input){
  return parseInt(input)==input;
}

//======================================================================== index.php
if (typeof $IS_INDEX_PHP != 'undefined')
{
  var $current_topic = -1;
  
  function scroll($i) {
    $("html:not(:animated),body:not(:animated)").animate({scrollTop : $i}, 0);
  }
  function goHash() {
    var words = window.location.hash.split('/');
    var $to_topic = words[1];
    var $to_reply = (words[2])?words[2]:1;
    if (isINT($to_topic) && isINT($to_reply))
    {
      var $this_topic = $("#topic_title").attr('name');
      var $this_reply = $("#text_container").find(".tcore:eq(0)").text();
      if (!$this_reply) $this_reply = 1;
      if (($to_topic != $this_topic) || ($to_reply != $this_reply)) 
      {
        goTopic($to_topic, $to_reply);
        return true;
      }
    }
    return false;
  }

  var $shall_go_end_of_mid = false;

  function updateCurrTopic(tttt)
  {
    if (tttt == $current_topic) return;
    $(".topic[name=" + $current_topic + "] p").removeClass('current');
    $current_topic = tttt;
    $(".topic[name=" + tttt + "] p").addClass('current');
  }
  function goTopic(tttt, $rep) {
    $('#prepare_post_button').contents().filter(function(){return this.nodeType == 3}).replaceWith('&nbsp;Create');
    var $topic_change = false;
    if ($current_topic != tttt)
    {
      $topic_change = true;
      updateCurrTopic(tttt);
    }
    var $begin = 0;
    if ($rep) { $begin = $rep; }
    $.post('post_db.php', {method:"get", topic:tttt, begin:$begin}, function(msg)
    {
      if ($topic_change)
      {
        $('#text_container').empty();
        if (!$shall_go_end_of_mid)
        {
          scroll(0);
        }
        clearPost();
        $('#post_title').hide();
        $('#text_container').show();
      }
      updateText(msg);
      
      if ($shall_go_end_of_mid)
      {
        $shall_go_end_of_mid = false;
        to_end_of_mid();
      }
    });
  }

  function refresh($to_topic) {
    $.post('post_db.php', {method:"get"}, function(msg)
    {
      updateNav(msg);
      if ($to_topic > 0)
      {
        scroll(0);
        goTopic($to_topic);
      }
      else if (typeof $to_topic == 'undefined')
      {
        scroll(0);
        if ($(".topic:eq(0)").attr('name') > 0)
          goTopic($(".topic:eq(0)").attr('name'), 0);
        else
          goTopic(0, 0);
      }
    });
  }  
  
  $(window).resize(function() {
  }).trigger("resize");
  
  function to_end_of_mid() {
    scroll($('#mid_container').height() - $(window).height() + 40);
  }

  function updateText(msg, $partial) {
    if (msg) {
      $('#text_container').html(msg);
      
      $('#mid').show();
      document.title = $('#topic_title').text();
      updateCurrTopic($('#topic_title').attr('name'));
      
      var $numposts = $('#topic_title').attr('topic_replies');
      var $target = $(".topic[name=" + $current_topic + "]").find('.trep');
      $target.text($numposts);
      if ($numposts > 0) $target.show(); else $target.hide();

      makeHash();
      if (typeof MathJax != 'undefined') if (typeof MathJax.isReady != 'undefined') if (MathJax.isReady)
        MathJax.Hub.Queue(["Typeset", MathJax.Hub, document.getElementById("text_container")]);
    }
    $("#text_container .tup").mousedown(function(){replyShift(-1); return false});
    $("#text_container .tup").hover(hover_a, hover_b);
    $("#text_container .tdown").mousedown(function(){replyShift(+1); return false});
    $("#text_container .tdown").hover(hover_a, hover_b);
  }
  function updateNav(msg) {
    $('#left').show();
    if (!msg) return;
    $('#nav').html(msg);
    if (typeof MathJax != 'undefined') if (typeof MathJax.isReady != 'undefined') if (MathJax.isReady)
      MathJax.Hub.Queue(["Typeset", MathJax.Hub, document.getElementById("nav")]);
    $(".topic[name=" + $current_topic + "] p").addClass('current');
    $("#nav .tup").mousedown(function(){topicShift(-1); return false});
    $("#nav .tup").hover(hover_a, hover_b);
    $("#nav .tdown").mousedown(function(){topicShift(+1); return false});
    $("#nav .tdown").hover(hover_a, hover_b);
    $("#nav .topic p").mousedown(function(e){
      e.preventDefault();
      goTopic($(this).parent().attr('name'));
    });
  }
  function clearPost() {
    $('#post_text').val('');
    $('#post_title').val('');
    $('#post_msg').empty();
  }
  
  function makeHash() {
    var $this_topic = $("#topic_title").attr('name');
    var $this_reply = $("#text_container").find(".tcore:eq(0)").text();
    if ((!$this_reply) || ($this_reply == 1))
    {
      window.location.hash = "topic/" + $this_topic;
    } else {
      window.location.hash = "topic/" + $this_topic + "/" + $this_reply;
    }
  }
  
  if (goHash()) refresh(-1); else  refresh();
  window.addEventListener("hashchange", goHash, false);
  
  $('#post_container').keydown(function (e) {
    if (e.ctrlKey && e.keyCode == 13) {
      $('#post_button').mousedown().mouseup();
    }
  });
  
  $('#refresh_button').mousedown(function(e)
  {
    e.preventDefault();
    refresh();
  });

  function replyShift($dir) {
    var $begin = 0;
    if ($dir == -1)
    {
      $begin = parseInt($('#topic_title').attr('begin')) - parseInt($('#topic_title').attr('limit'));
    } 
    else
    {
      $begin = parseInt($('#topic_title').attr('end')) + 1;
    }
    $.post('post_db.php', {method:"get", topic:$current_topic, begin:$begin}, function(msg)
    {
      scroll(0);
      updateText(msg);
    });        
  }
  
  function topicShift($dir) {
    var $begin = 0;
    if ($dir == -1)
    {
      $begin = parseInt($('#left_title').attr('begin')) - parseInt($('#left_title').attr('limit'));
    } 
    else
    {
      $begin = parseInt($('#left_title').attr('end')) + 1;
    }
    $.post('post_db.php', {method:"get", begin:$begin}, function(msg)
    {
      if (msg)
      {
        scroll(0);
        updateNav(msg);
      }
    });        
  }
  
  $('#page_bottom_button').mousedown(function(e)
  {
    e.preventDefault();
    to_end_of_mid();
  });
  $('#page_top_button').mousedown(function(e)
  {
    e.preventDefault();
    scroll(0);
  });
  
  var $current_topic_bak = -1;
  $('#prepare_post_button').mousedown(function(e)
  {
    e.preventDefault();
    clearPost();
    if ($current_topic >= 0)
    {
      $('#prepare_post_button').contents().filter(function(){return this.nodeType == 3}).replaceWith('&nbsp;Cancel');
      $current_topic_bak = $current_topic;
      $(".topic[name=" + $current_topic + "] p").removeClass('current');
      $current_topic = -1;
      $('#text_container').hide();
      $('#post_title').show();
    }
    else 
    {
      $('#prepare_post_button').contents().filter(function(){return this.nodeType == 3}).replaceWith('&nbsp;Create');
      $current_topic = $current_topic_bak;
      $(".topic[name=" + $current_topic + "] p").addClass('current');
      $('#post_title').hide();
      $('#text_container').show();
    }
  });
  
  var post_working = false;
  $('#post_button').mousedown(function(e)
  {
    e.preventDefault();
    if (post_working) return false;
    post_working = true;
    $('#post_msg').html('Posting...');
    $.post('post_db.php', {title:$('#post_title').val(), text:$('#post_text').val(), method:"new", topic:$current_topic}, function(msg)
    {
      if(msg.substr(0, 7) == 'SUCCESS')
      {
        clearPost();
        $('#post_msg').html('Success.');
        if ($current_topic < 0)
        {
          refresh(msg.substr(7));
        }
        else
        {
          $shall_go_end_of_mid = true;
          goTopic($current_topic);
        }
      }
      else
      {
        $('#post_msg').html(msg);
      }
      post_working = false;
    });
  });
  
  (function () {
    var head = document.getElementsByTagName("head")[0], script;
    script = document.createElement("script");
    script.type = "text/x-mathjax-config";
    script[(window.opera ? "innerHTML" : "text")] =
    'MathJax.Hub.Config({\n' +
      'jax: ["input/TeX","output/SVG"],\n' +
    'extensions: ["tex2jax.js","MathMenu.js","MathZoom.js"],\n' +
    'TeX: { extensions: ["AMSmath.js","AMSsymbols.js","noErrors.js","noUndefined.js"] },\n' +
    'tex2jax: { inlineMath: [["$","$"]] }\n' +
    '});';
    head.appendChild(script);
    script = document.createElement("script");
    script.type = "text/javascript";
    script.src = "http://cdn.mathjax.org/mathjax/latest/MathJax.js";
    head.appendChild(script);
  })();
}
//======================================================================== account.php
if (typeof $IS_ACCOUNT_PHP != 'undefined')
{
  $(document).keypress(function(e) {
    if(e.keyCode == 13) {
      $('#login_button').mousedown().mouseup();
    }
  });
  var working = false;
  $('#login_button').mousedown(function(e)
  {
    e.preventDefault();
    if (working) return false;
    working = true;
    $('#message').empty();
    if ($_GET['a'] == 'login')
    {
      $.post('account_db.php', {method:'login', user:$('#login_user').val(), pass:$('#login_pass').val()}, function(msg)
      {
        if (msg == "Success.")
        {
          window.location.href = "index.php";
        }
        $('#message').html(msg);
        working = false;
      });
    }
    else if ($_GET['a'] == 'register')
    {
      $.post('account_db.php', {method:'register', user:$('#login_user').val(), pass:$('#login_pass').val()}, function(msg)
      {
        if (msg == "Success.")
        {
          window.location.href = "index.php";
        }
        $('#message').html(msg);
        working = false;
      });
    }
  });
}
});