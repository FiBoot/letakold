angular.module('App').controller('imageCtrl', [
  '$scope',
  '$rootScope',
  '$routeParams',
  '$location',
  'ajaxService',
  function($scope, $rootScope, $routeParams, $location, ajaxService) {
    const IMAGE_PER_PAGE = 12;
    const HIDDEN_FILE_INPUT = '#hiddenFileInput';
    const ALOWED_EXTENTIONS = ['image/jpeg', 'image/png', 'image/gif'];
    const MAX_IMAGESIZE = 4500000;

    $scope.D = {
      info: new AjaxInfo(),
      loaded: false,
      list: []
    };
    $scope.I = {
      path: '../images',
      displayed: [],
      pageCount: 0,
      page: 0,
      selected: null
    };
    $scope.U = {
      name: '',
      filepath: '',
      progress: {
        completed: 0,
        completedStyle: '0%'
      },
      info: new AjaxInfo()
    };

    $scope.setPage = function setPage(page) {
      const index = (page - 1) * IMAGE_PER_PAGE;
      $scope.I.page = page;
      $scope.I.displayed = $scope.D.list.slice(index, index + IMAGE_PER_PAGE);
    };

    $scope.back = function back() {
      $scope.I.selected = null;
    };

    $scope.select = function select(image) {
      $scope.I.selected = image;
    };

    $scope.copy = function copy(image) {
      const link = `http://${$location.host()}/fiboot/-new/#/image/${image.id}`;
      copyToClipboard(link);
    };

    $scope.openModal = function openModal() {
      $('#image_modal').modal('show');
    };

    $scope.selectImageFile = function selectImageFile() {
      $(HIDDEN_FILE_INPUT).trigger('click');
    };

    $scope.setFilePath = function setFilePath() {
      $scope.U.filepath = document.querySelector(HIDDEN_FILE_INPUT).files[0].name;
      $rootScope.apply();
    };

    $scope.upload = function upload() {
      const file = document.querySelector(HIDDEN_FILE_INPUT).files[0];
      let message = null;

      $scope.U.info.ok();
      if (!file) {
        return $scope.U.info.nok('You must choose a file');
      }
      if (!message && ALOWED_EXTENTIONS.indexOf(file.type) < 0) {
        return $scope.U.info.nok('File must be jped, png or gif');
      }
      if (!message && file.size > MAX_IMAGESIZE) {
        return $scope.U.info.nok(`File is too large (max: ${MAX_IMAGESIZE / 100000}MB)`);
      }

      var xhr = new XMLHttpRequest();

      xhr.upload.onprogress = function(event) {
        if (event.lengthComputable) {
          $scope.U.progress.completed = Math.round((event.loaded / event.total) * 10000) / 100;
          $scope.U.progress.completedStyle = { width: $scope.U.progress.completed + '%' };
          $rootScope.apply();
        }
      };
      xhr.onload = function() {
        if (this.readyState === 4) {
          console.log(this.response);
        }
      };

      xhr.open('POST', 'api/upload.php', true);

      xhr.setRequestHeader('Content-Type', 'multipart/form-data');
      xhr.setRequestHeader('file_name', file.name);
      xhr.setRequestHeader('file_size', file.size);
      xhr.setRequestHeader('file_type', file.type);
      xhr.setRequestHeader('message', $scope.U.name);
      xhr.send(file);
    };

    function loadList(cb) {
      ajaxService.internalAjax(
        'list',
        { type: 'image', order: 'creation_date' },
        $scope.D.info,
        response => {
          if (response.status) {
            response.data.forEach(image => image.parseData());
            $scope.D.list = response.data;
            $scope.I.pageCount = Math.ceil(response.data.length / IMAGE_PER_PAGE);
            $scope.D.loaded = true;
            $scope.setPage(1);
            cb();
            $rootScope.apply();
          }
        }
      );
    }

    function loadImage(id) {
      const index = $scope.D.list.findIndex(i => i.id === id);
      const page = Math.floor(index / IMAGE_PER_PAGE) + 1;
      $scope.I.selected = $scope.D.list[index];
      $scope.setPage(page);
    }

    function init() {
      loadList(() => {
        if ($routeParams.hasOwnProperty('id')) {
          loadImage(parseInt($routeParams.id));
        }
      });
    }
    init();
  }
]);
