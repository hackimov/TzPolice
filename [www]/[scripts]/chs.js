angular.module("app", [])

.run(function ($rootScope) {
	$rootScope.infoVisible = false;
})

.service('mpBlack', function ($rootScope, $http) {

	var url = "_modules/mp/comm.php";

	this.data = {
		records: [{log: 11111111},{log: 22222222}],
	};

	this.requstRecords = function (id) {

		var self = this;
		var i = {};
		i.act = "get_records";
		i.bl_id = id;

		$http({method: 'POST', url: url, data: i}).then(function (response) {
	
			response.data.results.forEach(function (entry) {
			
				self.data.records.push(entry);	

			});

		});

	};

})

.controller('dossierCtrl', function ($scope, $rootScope, mpBlack) {

	$scope.records = mpBlack.data.records;
	$scope.show_dossier = function(id) {
		$rootScope.infoVisible = true;
	};
})