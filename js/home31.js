window.AppNamespace = {};
$(function () {
    AppNamespace.app = new DevExpress.framework.html.HtmlApplication({
        namespace: AppNamespace,
        defaultLayout: "default"
    });
    AppNamespace.app.router.register(":view", { view: "home" });
    AppNamespace.app.router.register(":view/:name", { view: "home", name: '' });
    AppNamespace.app.navigate();
});


AppNamespace.home = function () {
    var viewModel = {
        message: ko.observable('Welcome!'),
        maps: run(),
        name: ko.observable(''),
        //userVerification: userVerification(),
    }
    return viewModel;
};



AppNamespace.greeting = function (params) {
    var viewModel = {
        message: ko.observable('hello ' + params.name + '!'),
    };
    return viewModel;


};

//function userVerification() {
  //window.fbAsyncInit = function() {

//    }

//};
function run(){
    var _this = this;
    setTimeout(function() { _this.googleMaps() }, 500);
  }

function googleMaps() {
    if(document.readyState === "complete") {
        if(navigator.geolocation){
            navigator.geolocation.getCurrentPosition(showPosition, error);
        }
    }
}

function error(error){
    alert('error:/n' + error);
}

function showPosition(params) {
    cLat = params.coords.latitude;
    cLong = params.coords.longitude;
    var map_canvas = document.getElementById('map_canvas');
    var map_options = {
        center: new google.maps.LatLng(cLat, cLong),
        zoom: 16,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(map_canvas, map_options);

    var node=document.createElement("DIV");
    node.setAttribute('id','clock');
    var walker=document.createElement("DIV");
    walker.setAttribute('id','walker');
    walker.innerHTML = "Since Lesley walked Zippo";
    var time=document.createElement("DIV");
    time.setAttribute('id','time');
    node.appendChild(time);
    node.appendChild(walker);
    map_canvas.appendChild(node);
	getLastWalk();
    //t=setTimeout(function(){clock()},500);
}



function clock(lastWalk){
    var match = lastWalk.match(/^(\d+)-(\d+)-(\d+) (\d+)\:(\d+)\:(\d+)$/)
    //var s = lastWalk.split(/[-: ]/);
	//var walk = new Date(lastWalk[1], lastWalk[2] - 1, lastWalk[3], lastWalk[4], lastWalk[5], lastWalk[6]);
    //console.log(walk.getTime());
	//var walk = Date.parse(lastWalk);
	
	var s = lastWalk.split(/[-: ]/);
	var walk = new Date(s[0], s[1] - 1, s[2], s[3], s[4], s[5]).getTime();

	
    var today= new Date();
    var year = today.getFullYear();
    var month = today.getMonth();
    var day = today.getDate();
    var hours = today.getHours();
    var minutes = today.getMinutes();
    var seconds = today.getSeconds();
   
	var thisTime = new Date(year, month, day, hours, minutes, seconds).getTime();
    
    var timeDiff = (thisTime - walk) / 1000;

    var hour = timeDiff / 3600;
    hour = hour.toString();
    var hour = hour.substr(0, hour.indexOf('.'));
    var minute = (timeDiff - (hour * 3600))/60;
    minute = minute.toString();
    var minute = minute.substr(0, minute.indexOf('.'));
    var second = timeDiff - ((hour *3600) + (minute * 60));
	
	
    h=checkTime(hour);
    m=checkTime(minute);
    s=checkTime(second);
	
	
    document.getElementById('time').innerHTML=h+":"+m+":"+s;
    t=setTimeout(function(){clock(lastWalk)},1000);
}

function checkTime(i)
{
    if (i<10)
    {
        i="0" + i;
    }
    return i;
}

function gatherDogWalk(){
    navigator.geolocation.getCurrentPosition(writeDogWalk);
}

function writeDogWalk(params){
    console.log('run');

    FB.api('/me', function(response) {        
    var date =  new Date();
    var dateString = date.getFullYear() +"-"+ date.getMonth() +"-"+ date.getDate() +" "+ date.getHours() +":"+ date.getMinutes() +":"+ date.getSeconds();
    var who = response.id;
    var location = Array(params.coords.latitude, params.coords.longitude);
    var dog = $('.currentDog').data('dog-id');
    var time = dateString;
    var group = $('nav ul li.dx-button').data('group-id');

    console.log(response);
    //Lesleytaihitu.nl/dogwalker/
    $.ajax({
        url:'php/user.php',
        data:{"user_id":who, "location":location, "dog_id":dog, "datetime":time, "group_id":group, 'action':'insertWalk'},
        cache: false,
        content: false,
        crossDomain: true,
        proccessData: false,
        type: 'POST',
        success: function(){
			console.log('succes');
        },
        error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        alert(thrownError);
      }
    });
    });
}

function insertUser(){
    FB.api('/me', function(response) { 
    $.ajax({
        url:'php/user.php',
        data:{"user_id":response.id, "firstname":response.firstname, "lastname":response.lastname, "gender":response.gender, 'action':'insertUser'},
        cache: false,
        content: false,
        crossDomain: true,
        proccessData: false,
        type: 'POST',
        success: function(){
			console.log('succes');
        },
        error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        alert(thrownError);
      }
    });
    });
}

function getLastWalk(){
    FB.api('/me', function(response) { 
    $.ajax({
        url:'php/user.php',
        data:{'action':'getLastWalk', 'dog_id':$('.currentDog').data('dog-id')},
        cache: false,
        content: false,
        crossDomain: true,
        dataType: 'json',
        proccessData: false,
        type: 'POST',
        success: function(data){
            console.log(data);
            clock(data['datetime']);
            $('.currentDog').data('dog-id', data['dog_id']);
            $('#walker').html('Since '+data['user_id']+' walked '+data['dog_id']);
        },
        error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        alert(thrownError);
      }    });
    });
}

function getGroupInfo(){
    FB.api('/me', function(response) { 
    $.ajax({
        url:'php/user.php',
        data:{'action':'getGroupInfo', 'dog_id':$('.currentDog').data('dog-id')},
        //cache: false,
        //content: false,
        //crossDomain: true,
        dataType: 'json',
        //proccessData: false,
        type: 'POST',
        success: function(data){
            console.log(data);
        },
        error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        alert(thrownError);
      }
    });
    });
}

notificationTypes = ['info', 'warning', 'error', 'success'];
currentType = ko.observable(notificationTypes[0]);

showNotification = function () {
  DevExpress.ui.notify('Notification message', currentType, 2000);
};
$(document).ready(function(){
	window.fbAsyncInit = function() {
  console.log('done loading');
  FB.init({
    appId      : '582011021846910', // App ID
    //channelUrl : 'http://mysite.com/pages/account/channel.html', // Channel File
    status     : true, // check login status
    cookie     : true, // enable cookies to allow the server to access the session
    xfbml      : true  // parse XFBML
  });

  // Additional init code here
  FB.getLoginStatus(function(response) {
  if (response.status === 'connected') {
      // connected
  } else if (response.status === 'not_authorized') {
      // not_authorized
      FB.login();
  } else {
      // not_logged_in
      FB.login();
  }
  });
  getGroupInfo();

};

});




