angular.module('App').controller('storyCtrl', [
  '$scope',
  '$rootScope',
  '$routeParams',
  'ajaxService',
  function($scope, $rootScope, $routeParams, ajaxService) {
    class Choice {
      constructor(text, toId) {
        this.text = text;
        this.toId = toId;
      }
    }

    class Screen {
      constructor(id, text, choices, bg) {
        this.id = id;
        this.text = text;
        this.choices = choices;
        this.bg = bg;
        this.showbg = true;
      }
    }

    let gId = 1;
    // const json = `[{"id":1,"text":"Screen 1","choices":[{"text":"go 2","toId":2},{"text":"go 3","toId":3}],"bg":"bg/1.png","showbg":true},{"id":2,"text":"Screen 2","choices":[{"text":"back","toId":1},{"text":"go 3","toId":3}],"bg":"","showbg":false},{"id":3,"text":"Screen 3","choices":[{"text":"go 4","toId":4}],"bg":"","showbg":false},{"id":4,"text":"End","choices":[],"bg":"bg/4.png","showbg":true}]`;
    const json = `[{"id":1,"text":"bienvenue toussa","choices":[{"text":"choix n° 1","toId":1}],"bg":"","showbg":true}]`;

    $scope.S = {
      loaded: false,
      story: null
    };
    $scope.S = {
      editmode: false,
      list: [],
      selected: null
    };

    $scope.addScreen = function addScreen() {
      const choice = new Choice('choix', null);
      $scope.S.list.push(new Screen(gId, `Screen ${gId}`, [choice], ''));
      gId += 1;
      choice.toId = $scope.S.list[0].id;
    };

    $scope.deleteScreen = function deleteScreen(screen) {
      const rmId = screen.id;
      msplice($scope.S.list, screen);
      $scope.S.list.forEach(screen => {
        screen.choices.forEach(choice => {
          if (choice.toId === rmId) {
            choice.toId = $scope.S.list[0].id;
          }
        });
      });
    };

    $scope.addChoice = function addChoice(screen) {
      screen.choices.push(new Choice('', $scope.S.list[0].id));
    };

    $scope.removeChoice = function removeChoice(screen, choice) {
      msplice(screen.choices, choice);
    };

    $scope.saveStory = function saveStory() {
      console.log(angular.toJson($scope.S.list));
    };

    $scope.goTo = function goTo(choice) {
      const screen = $scope.S.list.find(s => s.id === choice.toId);
      if (screen) {
        $scope.S.selected = screen;
      } else {
        console.warn(`No screen found for ${choice.toId}`);
      }
    };

    function init() {
      // TODO ajax load?
      if (json) {
        const data = JSON.parse(json);
        data.forEach(screen => {
          let choices = [];
          screen.choices.forEach(choice => choices.push(new Choice(choice.text, choice.toId)));
          $scope.S.list.push(new Screen(screen.id, screen.text, choices, screen.bg));
        });
        $scope.S.loaded = true;
      }
    }
    init();
  }
]);
