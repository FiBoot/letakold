angular.module('App').controller('storyCtrl', [
  '$scope',
  '$rootScope',
  '$routeParams',
  '$location',
  'ajaxService',
  function($scope, $rootScope, $routeParams, $location, ajaxService) {
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

    $scope.D = {
      gId: 0,
      info: new AjaxInfo(),
      loaded: false,
      story: null
    };
    $scope.S = {
      editmode: false,
      list: [],
      selected: null
    };
    $scope.U = $rootScope.User;

    $scope.addScreen = function addScreen() {
      const choice = new Choice('choix', null);
      $scope.D.gId += 1;
      $scope.S.list.push(new Screen($scope.D.gId, `Screen ${$scope.D.gId}`, [choice], ''));
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
      const data = angular.toJson($scope.S.list);
      $scope.D.story.data = `${data}`;
      ajaxService.internalAjax(
        $scope.D.story.id ? 'save' : 'new',
        { item: $scope.D.story },
        $scope.D.info,
        response => {
          if (response.status && response.data) {
            $location.path(`/story/${response.data.id}`);
          }
        }
      );
    };

    $scope.goTo = function goTo(choice) {
      const screen = $scope.S.list.find(s => s.id === choice.toId);
      if (screen) {
        $scope.S.selected = screen;
      } else {
        console.warn(`No screen found for ${choice.toId}`);
      }
    };

    $scope.toggleEdit = function toggleEdit() {
      $scope.S.editmode = !$scope.S.editmode;
      if (!$scope.S.editmode) {
        $scope.reset();
      }
    };

    $scope.reset = function reset() {
      $scope.S.selected = $scope.S.list[0];
    };

    function parseData(story) {
      story.parseData();
      story.data.forEach(screen => {
        let choices = [];
        screen.choices.forEach(choice => choices.push(new Choice(choice.text, choice.toId)));
        $scope.S.list.push(new Screen(screen.id, screen.text, choices, screen.bg));
        $scope.D.gId = screen.id > $scope.D.gId ? screen.id : $scope.D.gId;
      });
      $scope.reset();
      $scope.D.story = story;
      $scope.D.loaded = true;
      $rootScope.apply();
    }

    function loadStory(id, cb) {
      ajaxService.internalAjax('get', { type: 'story', id: id }, $scope.D.info, response => {
        if (response.status) {
          cb(response.data);
        } else {
          $location.path('/story');
        }
      });
    }

    function init() {
      $scope.D.loaded = false;
      $scope.D.story = new DataRow({
        account_id: $rootScope.User.id,
        type: 'story'
      });
      if ($routeParams.hasOwnProperty('id')) {
        loadStory($routeParams.id, parseData);
      }
    }
    init();
  }
]);
