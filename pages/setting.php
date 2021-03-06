<?php
// ---------------------------------------------------------------------------- 
// Copyright © Lyon e-Sport, 2018
// 
// Contributeur(s):
//     * Ortega Ludovic - ludovic.ortega@lyon-esport.fr
// 
// Ce logiciel, AdminAFK, est un programme informatique servant à administrer 
// et gérer un tournoi CS:GO avec eBot et Toornament.
// 
// Ce logiciel est régi par la licence CeCILL soumise au droit français et
// respectant les principes de diffusion des logiciels libres. Vous pouvez
// utiliser, modifier et/ou redistribuer ce programme sous les conditions
// de la licence CeCILL telle que diffusée par le CEA, le CNRS et l'INRIA 
// sur le site "http://www.cecill.info".
// 
// En contrepartie de l'accessibilité au code source et des droits de copie,
// de modification et de redistribution accordés par cette licence, il n'est
// offert aux utilisateurs qu'une garantie limitée.  Pour les mêmes raisons,
// seule une responsabilité restreinte pèse sur l'auteur du programme,  le
// titulaire des droits patrimoniaux et les concédants successifs.
// 
// A cet égard  l'attention de l'utilisateur est attirée sur les risques
// associés au chargement,  à l'utilisation,  à la modification et/ou au
// développement et à la reproduction du logiciel par l'utilisateur étant 
// donné sa spécificité de logiciel libre, qui peut le rendre complexe à 
// manipuler et qui le réserve donc à des développeurs et des professionnels
// avertis possédant  des  connaissances  informatiques approfondies.  Les
// utilisateurs sont donc invités à charger  et  tester  l'adéquation  du
// logiciel à leurs besoins dans des conditions permettant d'assurer la
// sécurité de leurs systèmes et ou de leurs données et, plus généralement, 
// à l'utiliser et l'exploiter dans les mêmes conditions de sécurité. 
// 
// Le fait que vous puissiez accéder à cet en-tête signifie que vous avez 
// pris connaissance de la licence CeCILL, et que vous en avez accepté les
// termes.
// ----------------------------------------------------------------------------

include_once '../config/config.php';
include_once '../traitement/check_config.php';
include_once '../traitement/connect_bdd.php';
include_once '../traitement/verif_user.php';
include_once '../traitement/csrf.php';
include_once 'header.php';
include_once 'footer.php';
include_once 'navbar.php';

session_start();
$result_user = check_user($BDD_ADMINAFK, $_SESSION['login']);
if (!isset($_SESSION['login']) || ($result_user['login']!=$_SESSION['login']))
{
    $_SESSION['state']='1';
	$_SESSION['message']="You must be logged in to access this page";
	header('Location: '.$BASE_URL.'admin.php');
	exit();
}
$level=3;
if ($result_user['login']==$_SESSION['login'])
{
	if($result_user['level']>1)
	{
		$level=2;
		$_SESSION['state']='1';
		$_SESSION['message']="You must be Super-Admin to have access to this";
		header('Location: '.$BASE_URL.'admin.php');
		exit();
	}
	$level=1;
}
?>
<html>
	<head>
		<?php header_html('../', False, $CONFIG['url_glyphicon']); ?>
	</head>
	<body>
		<div class= "page-wrap">
			<?php
			$path_redirect ="";
			$path_redirect_disco ="../traitement/";
			$path_redirect_index="../";
			$path_img = "../images/";
			$current = "setting";
			if(!isset($CONFIG['url_ebot'])){$CONFIG['url_ebot'] = "";}
			if(!isset($CONFIG['toornament_api'])){$CONFIG['toornament_api'] = "";}
			if(!isset($CONFIG['toornament_client_id'])){$CONFIG['toornament_client_id'] = "";}
			if(!isset($CONFIG['toornament_client_secret'])){$CONFIG['toornament_client_secret'] = "";}
			if(!isset($CONFIG['toornament_id'])){$CONFIG['toornament_id'] = "";}
			if(!isset($CONFIG['display_connect'])){$CONFIG['display_connect'] = "";}
			if(!isset($CONFIG['display_veto'])){$CONFIG['display_veto'] = "";}
			if(!isset($CONFIG['display_bracket'])){$CONFIG['display_bracket'] = "";}
			if(!isset($CONFIG['display_participants'])){$CONFIG['display_participants'] = "";}
			if(!isset($CONFIG['display_schedule'])){$CONFIG['display_schedule'] = "";}
			if(!isset($CONFIG['display_stream'])){$CONFIG['display_stream'] = "";}
			display_navbar($current, $path_redirect, $path_redirect_disco, $path_redirect_index, $path_img, $level, $CONFIG['url_ebot'], $CONFIG['toornament_api'], $CONFIG['toornament_client_id'], $CONFIG['toornament_client_secret'], $CONFIG['toornament_id'], $CONFIG['display_connect'], $CONFIG['display_veto'], $CONFIG['display_bracket'], $CONFIG['display_participants'], $CONFIG['display_schedule'], $CONFIG['display_stream']);
			?>
			<div class="container">
				<br>
				<h1 class="text-center">Setting</h1>
				<br>
				<h6 class="text-center">General configuration of AdminAFK</h6>
				<br>
			</div>
			<div class="container">
			<?php
			echo "<br>";
			if(isset($_SESSION['state']) && !empty($_SESSION['state']) && isset($_SESSION['message']) && !empty($_SESSION['message']))
			{
				if($_SESSION['state']==1)
				{
					echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>".$_SESSION['message']."<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
					$_SESSION['state']='';
					$_SESSION['message']='';
				}
				else if ($_SESSION['state']==2)
				{
					echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>".$_SESSION['message']."<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
					$_SESSION['state']='';
					$_SESSION['message']='';
				}
				else if ($_SESSION['state']==3)
				{
					echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>".$_SESSION['message']."<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
					$_SESSION['state']='';
					$_SESSION['message']='';
				}
				echo "<br>";
			}
			?>
			</div>
			<div class="container">
			<?php
			$toornament_message = "";
			if(!isset($CONFIG['toornament_api']) || empty($CONFIG['toornament_api']))
			{
				$toornament_message = "api_key";
			}
			if(!isset($CONFIG['toornament_client_id']) || empty($CONFIG['toornament_client_id']))
			{
				if(!empty($toornament_message))
				{
					$toornament_message = $toornament_message.", client_id";
				}
				else
				{
					$toornament_message = "client_id";
				}
			}
			if(!isset($CONFIG['toornament_client_secret']) || empty($CONFIG['toornament_client_secret']))
			{
				if(!empty($toornament_message))
				{
					$toornament_message = $toornament_message.", client_secret";
				}
				else
				{
					$toornament_message = "client_secret";
				}
			}
			if(!isset($CONFIG['toornament_id']) || empty($CONFIG['toornament_id']))
			{
				if(!empty($toornament_message))
				{
					$toornament_message = $toornament_message.", default_id_toornament";
				}
				else
				{
					$toornament_message = "default_id_toornament";
				}
			}
			if(!empty($toornament_message))
			{
				echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>".$toornament_message." not filled some Toornament features are disable<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
			}
			$pages_display_message = "";
			if(!isset($CONFIG['display_connect']) || $CONFIG['display_connect'] == FALSE)
			{
				$pages_display_message = "Connect team";
			}
			if(!isset($CONFIG['display_veto']) || $CONFIG['display_veto'] == FALSE)
			{
				if(!empty($pages_display_message))
				{
					$pages_display_message = $pages_display_message.", Veto";
				}
				else
				{
					$pages_display_message = "Veto";
				}
			}
			if(isset($CONFIG['toornament_api']) && !empty($CONFIG['toornament_api']) && isset($CONFIG['toornament_id']) && !empty($CONFIG['toornament_id']))
			{
				if(!isset($CONFIG['display_bracket']) || $CONFIG['display_bracket'] == FALSE)
				{
					if(!empty($pages_display_message))
					{
						$pages_display_message = $pages_display_message.", Bracket";
					}
					else
					{
						$pages_display_message = "Bracket";
					}
				}
				if(!isset($CONFIG['display_participants']) || $CONFIG['display_participants'] == FALSE)
				{
					if(!empty($pages_display_message))
					{
						$pages_display_message = $pages_display_message.", Participants";
					}
					else
					{
						$pages_display_message = "Participants";
					}
				}
				if(!isset($CONFIG['display_schedule']) || $CONFIG['display_schedule'] == FALSE)
				{
					if(!empty($pages_display_message))
					{
						$pages_display_message = $pages_display_message.", Schedule";
					}
					else
					{
						$pages_display_message = "Schedule";
					}
				}
				if(!isset($CONFIG['display_stream']) || $CONFIG['display_stream']  == FALSE)
				{
					if(!empty($pages_display_message))
					{
						$pages_display_message = $pages_display_message.", Stream";
					}
					else
					{
						$pages_display_message = "Stream";
					}
				}
			}
			if(!empty($pages_display_message))
			{
				echo "<div class='alert alert-primary alert-dismissible fade show' role='alert'>".$pages_display_message." -> disabled<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
			}
			?>
			</div>
			<?php
			echo '<div class="container">';
				echo '<div class="card">';
					echo '<div class="card-header text-white bg-secondary">Other configuration</div>';
					echo '<div class="card-body">';
						echo '<div class="table-responsive">';
							echo '<table class="table table-bordered">';
								echo '<thead class="thead text-center">';
									echo '<tr>';
										echo '<th scope="col">eBot URL</th>';
										echo '<th scope="col">Path logo (glyphicon)</th>';
										echo '<th scope="col">Action</th>';
									echo '</tr>';
								echo '</thead>';
								echo '<tbody class="text-center">';
									echo '<form method="post" action="../traitement/setting.php">';
									echo '<tr>';
										echo '<td><input type="text" name="ebot" class="form-control" value="'.$CONFIG['url_ebot'].'"></td>';
										echo '<td><input type="text" name="logo_glyphicon" class="form-control" value="'.$CONFIG['url_glyphicon'].'"></td>';
										new_crsf("csrf_other_configuration");
										echo "<td class='text-center align-middle'><button type='submit' name='choice' value='other' class='btn btn-primary'>Update</button>";
									echo '</tr>';
									echo '</form>';
								echo '</tbody>';
							echo '</table>';
						echo '</div>';	
					echo '</div>';
				echo '</div>';
			echo '</div>';
			echo '<br>';
			echo '<div class="container">';
				echo '<div class="card">';
					echo '<div class="card-header text-white bg-secondary">Default eBot configuration</div>';
					echo '<div class="card-body">';
						echo '<form method="post" action="../traitement/setting.php">';
						echo '<div class="table-responsive">';
							echo '<table class="table table-bordered">';
								echo '<thead class="thead text-center">';
									echo '<tr>';
										echo '<th scope="col">Rules</th>';
										echo '<th scope="col">Password</th>';
										echo '<th scope="col">Match MMR</th>';
										echo '<th scope="col">Knife</th>';
										echo '<th scope="col">Action</th>';
									echo '</tr>';
								echo '</thead>';
								echo '<tbody class="text-center">';
									echo '<tr>';
										echo '<td><input type="text" name="rules" class="form-control" value="'.$CONFIG['default_ebot_rules'].'"></td>';
										echo '<td><input type="text" name="ebot_pass" class="form-control" value="'.$CONFIG['default_ebot_pass'].'"></td>';
										echo '<td>';
											echo '<select id="default_ebot_match_mmr" name="default_ebot_match_mmr" class="form-control">';
											if(isset($CONFIG['default_ebot_match_mmr']))
											{
												$tab_match_mmr = array("15", "12", "9", "5", "3");
												if($CONFIG['default_ebot_match_mmr'] == "15" || $CONFIG['default_ebot_match_mmr'] == "12" || $CONFIG['default_ebot_match_mmr'] == "9" || $CONFIG['default_ebot_match_mmr'] == "5" || $CONFIG['default_ebot_match_mmr'] == "3")
												{
													echo "<option selected>".$CONFIG['default_ebot_match_mmr']."</option>";
													for($i=0; $i<5;$i++)
													{
														if($tab_match_mmr[$i] != $CONFIG['default_ebot_match_mmr']){echo "<option>".$tab_match_mmr[$i]."</option>";}
													}
												}
												else
												{
													echo "<option selected>15</option>";
													echo "<option>12</option>";
													echo "<option>9</option>";
													echo "<option>5</option>";
													echo "<option>3</option>";
												}
											}
											else
											{
												echo "<option selected>15</option>";
												echo "<option>12</option>";
												echo "<option>9</option>";
												echo "<option>5</option>";
												echo "<option>3</option>";
											}
											echo '</select>';
										echo '</td>';
										echo '<td rowspan="3" class="text-center align-middle">';
											echo '<select id="knife" name="knife" class="form-control">';
											if(isset($CONFIG['default_ebot_knife']))
											{
												if($CONFIG['default_ebot_knife'] == "1")
												{
													echo "<option selected>Yes</option>";
													echo "<option>No</option>";
												}
												else
												{
													echo "<option selected>No</option>";
													echo "<option>Yes</option>";
												}
											}
											else
											{
												echo "<option selected>Yes</option>";
												echo "<option>No</option>";
											}
											echo '</select>';
										echo '</td>';
										echo "<td rowspan='3' class='text-center align-middle'><button type='submit' name='choice' value='match_ebot' class='btn btn-primary'>Update</button>";
									echo '</tr>';
									echo '<tr>';
										echo '<th scope="col">Overtime status</th>';
										echo '<th scope="col">Overtime MMR</th>';
										echo '<th scope="col">Overtime money</th>';
									echo '</tr>';
									echo '<tr>';
										echo '<td>';
											echo '<select id="overtime_status" name="overtime_status" class="form-control">';
											if(isset($CONFIG['default_ebot_ot_status']))
											{
												if($CONFIG['default_ebot_ot_status'] == "1")
												{
													echo "<option selected>Yes</option>";
													echo "<option>No</option>";
												}
												else
												{
													echo "<option selected>No</option>";
													echo "<option>Yes</option>";
												}
											}
											else
											{
												echo "<option selected>Yes</option>";
												echo "<option>No</option>";
											}
											echo '</select>';
										echo '</td>';
										echo '<td>';
											echo '<select id="overtime_mmr" name="overtime_mmr" class="form-control">';
											if(isset($CONFIG['default_ebot_ot_mmr']))
											{
												if($CONFIG['default_ebot_ot_mmr'] == 5)
												{
													echo "<option selected>5</option>";
													echo "<option>3</option>";
												}
												else
												{
													echo "<option selected>3</option>";
													echo "<option>5</option>";
												}
											}
											else
											{
												echo "<option selected>3</option>";
												echo "<option>5</option>";
											}
											echo '</select>';
										echo '</td>';
										echo '<td><input type="text" name="overtime_money" class="form-control" value="'.$CONFIG['default_ebot_ot_money'].'"></td>';
									echo '</tr>';
								echo '</tbody>';
							echo '</table>';
						echo '</div>';
						new_crsf("csrf_default_ebot");
						echo '</form>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
			echo '<br>';
			echo '<div class="container">';
				echo '<div class="card">';
					echo '<div class="card-header text-white bg-secondary">Toornament configuration</div>';
					echo '<div class="card-body">';
						echo '<div class="table-responsive">';
							echo '<table class="table table-bordered">';
								echo '<thead class="thead text-center">';
									echo '<tr>';
										echo '<th scope="col">API key</th>';
										echo '<th scope="col">Client ID</th>';
										echo '<th scope="col">Client secret</th>';
										echo '<th scope="col">Toornament ID</th>';
										echo '<th scope="col">Action</th>';
									echo '</tr>';
								echo '</thead>';
								echo '<tbody class="text-center">';
									echo '<form method="post" action="../traitement/setting.php">';
									echo '<tr>';
										echo '<td><input type="text" name="api_key" class="form-control" value="'.$CONFIG['toornament_api'].'"></td>';
										echo '<td><input type="text" name="client_id" class="form-control" value="'.$CONFIG['toornament_client_id'].'"></td>';
										echo '<td><input type="text" name="client_secret" class="form-control" value="'.$CONFIG['toornament_client_secret'].'"></td>';
										echo '<td><input type="text" name="toornament_id" class="form-control" value="'.$CONFIG['toornament_id'].'"></td>';
										new_crsf("csrf_toornament");
										echo "<td class='text-center align-middle'><button type='submit' name='choice' value='toornament' class='btn btn-primary'>Update</button>";
									echo '</tr>';
									echo '</form>';
								echo '</tbody>';
							echo '</table>';
						echo '</div>';	
					echo '</div>';
				echo '</div>';
			echo '</div>';
			echo '<br>';
			if(isset($CONFIG['toornament_api']) && !empty($CONFIG['toornament_api']) && isset($CONFIG['toornament_id']) && !empty($CONFIG['toornament_id']))
			{
				echo '<div class="container">';
					echo '<div class="card">';
						echo '<div class="card-header text-white bg-secondary">Steam / SteamID API configuration</div>';
						echo '<div class="card-body">';
							echo '<div class="table-responsive">';
								echo '<table class="table table-bordered">';
									echo '<thead class="thead text-center">';
										echo '<tr>';
											echo '<th scope="col">Steam API key</th>';
											echo '<th scope="col">SteamID API key</th>';
											if(isset($CONFIG['steam_api']) && !empty($CONFIG['steam_api']) && isset($CONFIG['steamid_api']) && !empty($CONFIG['steamid_api']))
											{
												echo '<th scope="col">Display VAC Ban</th>';
											}
											echo '<th scope="col">Action</th>';
										echo '</tr>';
									echo '</thead>';
									echo '<tbody class="text-center">';
										echo '<form method="post" action="../traitement/setting.php">';
										echo '<tr>';
											echo '<td><input type="text" name="steam_api" class="form-control" value="'.$CONFIG['steam_api'].'"></td>';
											echo '<td><input type="text" name="steamid_api" class="form-control" value="'.$CONFIG['steamid_api'].'"></td>';
											if(isset($CONFIG['steam_api']) && !empty($CONFIG['steam_api']) && isset($CONFIG['steamid_api']) && !empty($CONFIG['steamid_api']))
											{
												if($CONFIG['display_vac_ban']){ $vac_ban_checked = "checked";}else{ $vac_ban_checked ="";}
												echo '<td><div class="input-group-prepend d-flex justify-content-center"><div class="input-group-text"><input type="checkbox" name="vac_ban" value="1" '.$vac_ban_checked.' aria-label="Vac Ban">&nbspYes</div></div></td>';
											}
											new_crsf("csrf_steam_api");
											echo "<td class='text-center align-middle'><button type='submit' name='choice' value='steam_api' class='btn btn-primary'>Update</button>";
										echo '</tr>';
										echo '</form>';
									echo '</tbody>';
								echo '</table>';
							echo '</div>';	
						echo '</div>';
					echo '</div>';
				echo '</div>';
				echo '<br>';
			}
			echo '<div class="container">';
				echo '<div class="card">';
					echo '<div class="card-header text-white bg-danger">Activate the following pages</div>';
					echo '<div class="card-body">';
						echo '<div class="table-responsive">';
							echo '<table class="table table-bordered">';
								echo '<thead class="thead text-center">';
									echo '<tr>';
										echo '<th scope="col">Connect team</th>';
										echo '<th scope="col">Veto</th>';
										if(isset($CONFIG['toornament_api']) && !empty($CONFIG['toornament_api']) && isset($CONFIG['toornament_id']) && !empty($CONFIG['toornament_id']))
										{
											echo '<th scope="col">Bracket</th>';
											echo '<th scope="col">Participants</th>';
											echo '<th scope="col">Schedule</th>';
											echo '<th scope="col">Stream</th>';
										}
										echo '<th scope="col">Action</th>';
									echo '</tr>';
								echo '</thead>';
								echo '<tbody class="text-center">';
									echo '<tr>';
										echo '<form method="post" action="../traitement/setting.php">';
											if($CONFIG['display_connect']){ $connect_checked = "checked";}else{ $connect_checked ="";}
											if($CONFIG['display_veto']){ $veto_checked = "checked";}else{ $veto_checked ="";}
											if(isset($CONFIG['toornament_api']) && !empty($CONFIG['toornament_api']) && isset($CONFIG['toornament_id']) && !empty($CONFIG['toornament_id']))
											{
												if($CONFIG['display_bracket']){ $bracket_checked = "checked";}else{ $bracket_checked ="";}
												if($CONFIG['display_participants']){ $participants_checked = "checked";}else{ $participants_checked ="";}
												if($CONFIG['display_schedule']){ $schedule_checked = "checked";}else{ $schedule_checked ="";}
												if($CONFIG['display_stream']){ $stream_checked = "checked";}else{ $stream_checked ="";}
											}
											echo '<td><div class="input-group-prepend d-flex justify-content-center"><div class="input-group-text"><input type="checkbox" name="connect_team_view" value="1" '.$connect_checked.' aria-label="connect_team">&nbspYes</div></div></td>';
											echo '<td><div class="input-group-prepend d-flex justify-content-center"><div class="input-group-text"><input type="checkbox" name="veto_view" value="1" '.$veto_checked.' aria-label="veto">&nbspYes</div></div></td>';
											if(isset($CONFIG['toornament_api']) && !empty($CONFIG['toornament_api']) && isset($CONFIG['toornament_id']) && !empty($CONFIG['toornament_id']))
											{
												echo '<td><div class="input-group-prepend d-flex justify-content-center"><div class="input-group-text"><input type="checkbox" name="bracket_view" value="1" '.$bracket_checked.' aria-label="bracket">&nbspYes</div></div></td>';
												echo '<td><div class="input-group-prepend d-flex justify-content-center"><div class="input-group-text"><input type="checkbox" name="participants_view" value="1" '.$participants_checked.' aria-label="participants">&nbspYes</div></div></td>';
												echo '<td><div class="input-group-prepend d-flex justify-content-center"><div class="input-group-text"><input type="checkbox" name="schedule_view" value="1" '.$schedule_checked.' aria-label="schedule">&nbspYes</div></div></td>';
												echo '<td><div class="input-group-prepend d-flex justify-content-center"><div class="input-group-text"><input type="checkbox" name="stream_view" value="1" '.$stream_checked.' aria-label="stream">&nbspYes</div></div></td>';
											}
											else
											{
												echo "<input type=hidden name='bracket_view' value=".$CONFIG['display_bracket'].">";
												echo "<input type=hidden name='participants_view' value=".$CONFIG['display_participants'].">";
												echo "<input type=hidden name='schedule_view' value=".$CONFIG['display_schedule'].">";
												echo "<input type=hidden name='stream_view' value=".$CONFIG['display_stream'].">";
											}
											new_crsf("csrf_pages");
											echo "<td class='text-center'><button type='submit' name='choice' value='pages' class='btn btn-danger'>Update</button>";
										echo '</form>';
									echo '</tr>';
								echo '</tbody>';
							echo '</table>';
						echo '</div>';	
					echo '</div>';
				echo '</div>';
			echo '</div>';
			?>
			<br>
			<br>
		</div>
		<?php
		$path_img = "../images/";
		display_footer($path_img);
		?>
	</body>
</html>