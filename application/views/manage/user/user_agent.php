<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php echo $_SESSION['SYSCONFIG']['sysconfig_site_name']; ?></title>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('static/manage'); ?>/style/common.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('static/manage'); ?>/style/style.css" />

    <link rel="stylesheet" href="<?php echo base_url('static/layui/css'); ?>/layui.css">
    
    <!--[if lt IE 9]>
    <script src="<?php echo base_url('static/manage'); ?>/js/css3.js"></script>
    <script src="<?php echo base_url('static/manage'); ?>/js/html5.js"></script>
    <![endif]-->

    <script src="<?php echo base_url('static/manage'); ?>/js/common.js"></script>
    <script src="<?php echo base_url('static/layui'); ?>/layui.js"></script>

    <script src="/static/manage/js/jquery-1.8.0.min.js"></script>   
    <script src="/static/editor.md/editormd.js"></script>   
</head>
<body>

    <div class="pagebox">
        
        <div class="pagetitle layui-bg-black">
            用户管理 > 代理商管理
        </div>

        <style type="text/css">
            
            .myuploadbox{ width: 300px; }
            .imgpreview{ height: 36px; margin-left: 10px; position: relative; display: none; border: rgb(230, 230, 230) solid 1px; cursor: pointer; }
            .layui-table .imgpreview{ height: 30px; }
            tbody .layui-form-switch{ margin-top: 0px; }
        </style>

        <div class="mainbox">

            <style type="text/css">
                .search_box{ margin-bottom: 20px; }
                .search_box .search_value{ width: 300px; float: left; }
                .search_box .search_btn, .search_box .clear_search_btn{ float: left; margin-left: 10px; }
            </style>
            <div class="search_box layui-form">
                <input type="text" id="search_value" placeholder="搜索用户名、手机、邮箱" autocomplete="off" class="layui-input search_value" value="<?php echo $search; ?>">
                <button type="button" class="layui-btn search_btn" id="search_btn" data-url="/manage/user/agent">搜索用户</button>
                <a class="layui-btn layui-btn-normal clear_search_btn" href="/manage/user/agent">清空</a>
                <div class="clear"></div>
            </div>
            
            <table class="layui-table" >
                <colgroup>
                    <col>
                    <col>
                    <col>
                    <col style="width: 70px;">
                    <col style="width: 330px;">
                </colgroup>
                <thead>
                    <tr>
                        <th>用户名</th>
                        <th>邀请数量</th>
                        <th>机构码</th>
                        <th>代理商</th>
                        <th>操作</th>
                    </tr> 
                </thead>
                <tbody class="layui-form">

                    <?php if(count($userList)){ foreach($userList as $user){ ?>
                    <tr style="position: relative;">
                        <td><?php echo $user['user_name']; ?></td>
                        <td><?php echo $user['user_invite_count']; ?></td>
                        <td><?php echo $user['user_invite_code']; ?></td>
                        <td class="layui-form-item" style="width: 70px;">
                            <input type="checkbox" class="user_agent_switch" lay-skin="switch" lay-text="开启|关闭" data-user="<?php echo $user['user_id']; ?>" <?php echo $user['user_agent'] == 1 ? 'value="1" checked' : 'value="0"'; ?>>
                        </td>
                        <td style="position: relative; width: 330px;">

                            <a class="layui-btn layui-btn-xs loginbtn" data-id="<?php echo $user['user_id']; ?>">前台登陆</a>

                            <a class="layui-btn layui-btn-xs sendbtn window_show" data-title="代理商 - <?php echo $user['user_name']; ?> - 下级充值" data-href="/manage/user/agent/son_recharge/<?php echo $user['user_id']; ?>">下级充值</a>

                            <a class="layui-btn layui-btn-xs sendbtn window_show" data-title="代理商 - <?php echo $user['user_name']; ?> - 下级提现" data-href="/manage/user/agent/son_withdraw/<?php echo $user['user_id']; ?>">下级提现</a>

                            <a class="layui-btn layui-btn-xs sendbtn window_show" data-title="代理商 - <?php echo $user['user_name']; ?> - 资产详情" data-href="/manage/user/asset/user_asset/<?php echo $user['user_id']; ?>">资产详情</a>

                            <?php if($user['user_agent']){ ?>
                                <a class="layui-btn layui-btn-xs invitebtn window_show" data-title="代理商 - <?php echo $user['user_name']; ?> - 邀请列表" data-href="/manage/user/user/user_invite/<?php echo $user['user_id']; ?>">邀请列表</a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php }} ?>
                </tbody>
            </table>

            <!-- 分页 -->
            <?php $this->load->view('manage/paging'); ?>
        </div>


    </div>
    
    <script>
      
        //JavaScript代码区域
        layui.use(['element', 'jquery', 'form', 'layer', 'upload', 'laydate'], function () {

            var element = layui.element;
            var form    = layui.form;
            var layer   = layui.layer;
            var upload  = layui.upload;
            var laydate = layui.laydate;
            var $       = layui.$;

            var layuiOpenIndex = 0;
            var layuiLoadIndex = 0;

            $('#search_btn').click(function(){

                var searchValue = $.trim($('#search_value').val());

                if (searchValue == '') {

                    layer.msg('请输入搜索内容');
                }else{

                    layuiLoadIndex = layer.load();
                    window.location.href = $(this).attr('data-url') + '?search=' + searchValue;
                }
            });

            //兼容layui的switch
            form.on('switch', function(data){

                if (data.elem.checked) {

                    $(data.elem).val(1).prop('checked', true);
                }else{

                    $(data.elem).val(0).prop('checked', false);
                }

                //代理商开关
                if ($(data.elem).hasClass('user_agent_switch')) {

                    layuiLoadIndex = layer.load();

                    $.ajax({
                        url: '/manage/user/user/change_agent',
                        type: 'post',
                        data: {

                            user_id : $(data.elem).attr('data-user'),
                            user_agent : $(data.elem).val()
                        },
                        dataType: 'json',
                        success: function (data) {
                            
                            layer.close(layuiLoadIndex);
                            layer.msg(data.message);

                            if (data.status) {

                                layer.close(layuiOpenIndex);
                                setTimeout(function(){

                                    window.location.reload();
                                }, 1000);
                            }else{

                                window.location.reload();
                            }
                        },
                        error: function(){

                            window.location.reload();

                            layer.close(layuiLoadIndex);
                            layer.msg('网络繁忙，请稍后再试');
                        }
                    });
                }
            });

            $('.loginbtn').click(function(){

                var _this = $(this);

                layer.load();

                $.ajax({
                    url: '/manage/user/user/login',
                    type: 'post',
                    data: {
                        'user_id' : _this.attr('data-id')
                    },
                    success: function (data) {
                        
                        window.open('/account', 'target');
                    },
                    complete: function(){

                        layer.closeAll();
                    }
                });
            });


            $('.window_show').click(function(){

                var _this = $(this);

                layuiOpenIndex = layer.open({

                    title: _this.attr('data-title'),
                    type: 2,
                    content : _this.attr('data-href'),
                    icon: 0,
                    maxmin: true,
                    area: ['100%', '100%'],
                    skin: 'my-layer-green',
                    btn: ['关闭'],
                    success : function(_dom, _index){

                        layer.full(_index);
                    }
                });

                return false;
            });
        });
    </script>
</body>
</html>