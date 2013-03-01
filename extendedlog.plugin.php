<?php

class ExtendedLog extends Plugin
{
	function action_admin_header()
	{
		$url = URL::get('auth_ajax', 'context=extendedlog');

		$script = <<< SCRIPT
$(function(){
	var initi = itemManage.initItems;
	itemManage.initItems = function(){
		initi();
		$('.page-logs .manage .item .less,.page-logs .manage .item .message.minor').hide();
		$('.page-logs .manage .item .more').show().css({clear: 'both', marginLeft: '40px', fontWeight: 'bold', width: '100%'});
		$('.page-logs .manage .item').click(function(){
			$('.extendedlog').remove();
			$(this).after('<div class="extendedlog"><div class="textarea" style="white-space:pre;font-family:consolas,courier new,monospace;border:1px solid #999;padding:20px;margin:20px 0px;height:100px;overflow-y:auto;">Loading...</div></div>');
			$('.extendedlog .textarea').resizeable();
			$.post(
				'{$url}',
				{
					log_id: $('.checkbox input', $(this)).attr('id').match(/\[([0-9]+)\]/)[1]
				},
				function(result){
					$('.extendedlog .textarea').html(result)
				}
			);
		});
	}
});
SCRIPT;
		Stack::add('admin_header_javascript', $script, 'extendedlog', array('jquery', 'admin'));
	}

	function action_auth_ajax_extendedlog($handler)
	{
		$log = EventLog::get(array('fetch_fn' => 'get_row', 'id' => $handler->handler_vars['log_id'], 'return_data' => true));
		if(trim($log->data) == '') {
			$log->data = 'No additional data was logged.';
		}
		echo $log->message . "<hr>\n";
		echo $log->data;
	}

}
?>
