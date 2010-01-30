<!-- switch tomorrows_events on -->
<table width="170" border="0" cellpadding="0" cellspacing="0" class="calborder">
	<tr>
		<td align="center" class="sideback"><div style="height: 17px; margin-top: 3px;" class="G10BOLD">{L_TOMORROWS}</div></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF" align="left">
			<div style="padding: 5px;">
				<!-- switch t_allday on -->
				{T_ALLDAY}<br />
				<!-- switch t_allday off -->
				<!-- switch t_event on -->
				&bull; {T_EVENT}<br />
				<!-- switch t_event off -->
			</div>
		</td>
	</tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
	</tr>
</table>
<img src="images/spacer.gif" width="1" height="10" alt=" " /><br />

<!-- switch tomorrows_events off -->

<!-- switch vtodo on -->

<table width="170" border="0" cellpadding="0" cellspacing="0" class="calborder">
	<tr>
		<td align="center" width="98%" class="sideback"><div style="height: 17px; margin-top: 3px;" class="G10BOLD">{L_TODO}</div></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF" align="left">
			<div style="padding: 5px;">
				<table cellpadding="0" cellspacing="0" border="0">
					<!-- switch show_completed on -->
					<tr>
						<td><img src="images/completed.gif" alt=" " width="13" height="11" border="0" align="middle" /></td>
						<td><img src="images/spacer.gif" width="2" height="1" border="0" alt="" /></td>
						<td><s><a class="psf" href="javascript:openTodoInfo('{VTODO_ARRAY}')"><font class="G10B"> {VTODO_TEXT}</font></a></s></td>
					</tr>
					<!-- switch show_completed off -->
					<!-- switch show_important on -->
					<tr>
						<td><img src="images/important.gif" alt=" " width="13" height="11" border="0" align="middle" /></td>
						<td><img src="images/spacer.gif" width="2" height="1" border="0" alt="" /></td>
						<td><a class="psf" href="javascript:openTodoInfo('{VTODO_ARRAY}')"><font class="G10B"> {VTODO_TEXT}</font></a></td>
					</tr>
					<!-- switch show_important off -->
					<!-- switch show_normal on -->
					<tr>
						<td><img src="images/not_completed.gif" alt=" " width="13" height="11" border="0" align="middle" /></td>
						<td><img src="images/spacer.gif" width="2" height="1" border="0" alt="" /></td>
						<td><a class="psf" href="javascript:openTodoInfo('{VTODO_ARRAY}')"><font class="G10B"> {VTODO_TEXT}</font></a></td>
					</tr>
					<!-- switch show_normal off -->
				</table>
			</div>
		</td>
	</tr>			
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
	</tr>
</table>
<img src="images/spacer.gif" width="1" height="10" alt=" " /><br />


<!-- switch vtodo off -->

{MONTH_SMALL|-1}
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
	</tr>
</table>
<img src="images/spacer.gif" width="1" height="10" alt=" " /><br />

{MONTH_SMALL|+0}
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
	</tr>
</table>
<img src="images/spacer.gif" width="1" height="10" alt=" " /><br />

{MONTH_SMALL|+1}
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
	</tr>
</table>
