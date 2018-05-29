//修改上传的接口调用
require(['upload'], function (Upload) {
    var _FileUploaded = Upload.events.FileUploaded;
    Upload.events.FileUploaded = function (up, file, info) {
        //因为不同上传所返回的数据格式不同，你可以在这里进行处理
        _FileUploaded.apply(this, [up, file, info]);
    };
});