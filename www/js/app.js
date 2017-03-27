// Ionic Starter App

// angular.module is a global place for creating, registering and retrieving Angular modules
// 'starter' is the name of this angular module example (also set in a <body> attribute in index.html)
// the 2nd parameter is an array of 'requires'
angular.module('dictionary', ['ionic', 'app.directives'])

.run(function($ionicPlatform) {
    $ionicPlatform.ready(function() {
      if (window.cordova && window.cordova.plugins.Keyboard) {
        // Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
        // for form inputs)
        cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);

        // Don't remove this line unless you know what you are doing. It stops the viewport
        // from snapping when text inputs are focused. Ionic handles this internally for
        // a much nicer keyboard experience.
        cordova.plugins.Keyboard.disableScroll(true);
      }
      if (window.StatusBar) {
        StatusBar.styleDefault();
      }
    });
  })
  .controller('dictCtrl', function($scope, $http, $location, $timeout, $rootScope) {

    document.addEventListener("deviceready", onDeviceReady, false);

    function onDeviceReady() {
      document.addEventListener("online", onlineHandler, false);
      document.addEventListener("offline", offlineHandler, false);
    }

    function onlineHandler() {
      alert("online");
    }

    function offlineHandler() {
      alert("offline");
    }
    /**
     * Handle the received transcript here.
     * The result from the Web Speech Recognition will
     * be set inside a $rootScope variable. You can use it
     * as you want.
     */
    $scope.displayTranscript = function () {
        $scope.data.word  = $rootScope.transcript;
        //This is just to refresh the content in the view.
        if (!$scope.$$phase) {
            $scope.$digest();
        }
    }
    $scope.data = {};
    $scope.loader = false;
    $scope.wordSearched = false;
    $scope.audioFile = "";
    $scope.submit = function(formData) {
      if (formData.$invalid) {
        return;
      }
      $scope.loader = true;
      var link = 'http://www.dinkarkumar.com/dict.php';
      $http.post(link, {
        word: $scope.data.word
      }).then(function(res) {
        $scope.loader = false;
        if (res.data) {
          $scope.success = true;
          $scope.wordSearched = true;
          $scope.response = res.data;
          $scope.searchedWord = $scope.data.word;
          angular.forEach($scope.response, function(value, key) {
            $scope.phoneticSpelling = '- ' + value.pronunciations[0].phoneticSpelling;
            $scope.audioFile = value.pronunciations[0].audioFile;
            $timeout(function() {
              document.getElementById('myAudio').src = $scope.audioFile;
            }, 100);
          });
        } else {
          $scope.success = false;
        }
      });
    };
    $scope.play = function(url) {
      var x = document.getElementById("myAudio");
      x.play();
    }
  });