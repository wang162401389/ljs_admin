var app = getApp();
Page({

  data: {
    my: {},
    pageInfo: { article: {} }
  },

  onLoad: function (options) {
    var that = this;
    //这里读取关闭我们信息
    app.request('/page/aboutme', function (data) {
      var content = data.pageInfo.content;
      data.pageInfo.article = app.towxml.toJson(content, 'html');
      that.setData({ my: data.my, pageInfo: data.pageInfo });
    });
  },
})