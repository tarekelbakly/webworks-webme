		</div>
		</div>
		<?php
			echo WW_getScripts();
			echo '<!-- page generated in '.(microtime()-$webme_start_time).' seconds -->';
		?>
		<script type="text/javascript">var plugins_to_load={<?php echo join(',',$plugins_to_load); ?>};</script>
	</body>
</html>
