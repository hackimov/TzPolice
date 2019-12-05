<?php
$temp_user_avatar = 'avat-default-man.gif';
$temp_user_avatarhref = '<a href="?act=register">';
if(AuthStatus==1) {
  if (AuthUserAvatarClan <> '') { $temp_user_avatar = AuthUserAvatarClan; $temp_user_avatarhref = '<a href="http://www.timezero.ru/info.html?'.AuthUserName.'" target="_blank">'; }
}
?>
                <td width="34" height="66"><img src="i/empty.gif" width="34" height="66"></td>
                <td><?=$temp_user_avatarhref?><img src="i/avatar/<?=$temp_user_avatar?>" width="60" height="60" hspace="3" vspace="3" border="0"></a></td>
                <td width="13"><img src="i/empty.gif" width="13" height="66"></td>
              </tr>
            </table></td>
            <td background="i/bgr-powerfield.gif" class="repeat"><table width="100%" height="76"  border="0" cellpadding="15" cellspacing="0" background="i/bgr-powerfield-right.gif" class="tab-top-right-norepeat">
              <tr>
                <td nowrap background="i/bgr-powerfield-left.gif" class="norepeat"><img src="i/empty.gif" width="180" height="1"><br>
<?if(AuthStatus!=1 && AuthStatus!=2) {?>
                 <p class="d-text">Пользователь не идентифицирован</p>
                 <p align=left><a href="?act=register" class="d-menulink"> &raquo; регистрация</a><br>
                 <a class='d-menulink' href='?act=user_remind'> &raquo; выслать пароль</a></p>
<?}
if(AuthStatus==2) echo '<p><font color=red>указан неверный пароль для пользователя <B>'.$_REQUEST['AuthUser'].'</B></font></p>
                    <p align=left><a href="?act=register" class="d-menulink"> &raquo; регистрация</a> <a class="d-menulink" href="?act=user_remind"> &raquo; выслать пароль</a></p>';
if(AuthStatus==1) echo '<p class="d-text">Здравствуйте, <B>'.AuthUserName.'</B><br>
                  <a class="d-menulink" href="?act=user_home"> &raquo; Настройки</a><br>
                  <a class="d-menulink" href="?act='.$module.'&logoff=1"> &raquo; Выход</a>';
?>
                 </td>
              </tr>
            </table></td>
          </tr>
        </table></td>
        <td width="34" height="76" valign="bottom"><img src="i/device-02a.gif" width="34" height="76"></td>
        <td width="224" height="76" valign="bottom"><a class="d-menulink" href="#buygold"><img src="dealer.gif" width="224" height="70" border="0" alt="Купить золото от GameDealeR"></img></a></td>
<td width="100%" height="76" align="right" valign="bottom">
<img src="i/bgr-grid-end1.gif" width="16" height="76"></td>      </tr>
      <tr>
        <td width="280" height="28"><table width="250" height="28"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td background="i/auth-base.gif" class="repeat"><table width="100%" height="28"  border="0" cellpadding="2" cellspacing="0" background="i/auth-norepeat.gif" class="norepeat">
              <tr>
<?
if(AuthStatus!=1) {
        echo '<td width="36" align="right" nowrap class="d-text">
        <form name="auth" method="POST" style="margin-bottom:0px;margin-top:0px">
        <input name="act" type="hidden" value="'.$module.'">login:</td>
        <td width="50%"><input name="AuthUser" type="text" class="input-dark"></td>
        <td width="30" nowrap class="d-text">password:</td>
        <td><input name="AuthPass" type="password" class="input-dark"></td>';
}
if(AuthStatus==1) echo '<td align="center" nowrap class="d-text" colspan=4>'.AuthUserName.'</td>';
?>
              </tr>
            </table></td>
            <td width="45" height="28" align="center" background="i/auth-end.gif">
<?if(AuthStatus!=1) {?>
               <input type="image" class="norepeat" src="i/butt-go.gif" align="middle" width="26" height="20" border="0">
               </form>
<?
}
if(AuthStatus==1) echo '<a href="?act='.$module.'&logoff=1"><img src="i/butt-logout.gif" align="middle" width="26" height="20" border="0" ALT="выход"></a>';
?>
           </td>
          </tr>
       </table></td>