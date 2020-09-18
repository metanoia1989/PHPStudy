/**
 * Created by 辰光网络QQ:204455206 on 2020/2/10.
 */

function choose(obj)
	{
		var vid = $(obj).attr('data-visiter-id');
		var data =chat_data['visiter'+vid];
		$.cookie("cu_com", JSON.stringify(data),{ expires: 7, path: CGWL_ROOT_URL + '/admin/index' });
		window.location.href = CGWL_ROOT_URL + '/admin/index/chats?id=' + vid;
	}

	//表单提交
	function swith(){
		var form = document.getElementById("swith");
		form.submit();
	}

	function chooseGroup(){
		var form = document.getElementById("chooseGroup");
		form.submit();
	}

	