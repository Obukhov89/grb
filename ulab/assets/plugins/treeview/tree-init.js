let TreeView = function () {

    return {
        //main function to initiate the module
        init: function () {

            var DataSourceTree = function (options) {
                this._data  = options.data;
                this._delay = options.delay;
            };

            DataSourceTree.prototype = {

                data: function (options, callback) {
                    var self = this;

                    setTimeout(function () {
                        var data = $.extend(true, [], self._data);

                        callback({ data: data });

                    }, this._delay)
                }
            };

            var treeDataSource = new DataSourceTree({
                    data: [],
                    delay: 400
                });

            $('body').find('.FlatTree').tree({
                dataSource: treeDataSource,
            });
        }
    };
}();
