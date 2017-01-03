<?php

$intro_content = 
	'
		Serwis nie został do końca zainstalowany lub skonfigurowany. Aby w pełni zainstalować serwis, proszę wykonać następujące kroki:
		<ol>
			<li>
				Wgrać przez <b>FTP</b> pliki serwisu na hosting. Za pomocą komendy <b>chmod</b> zmienić atrybuty dla katalogów: <b>docs</b>, <b>images</b>, <b>sounds</b>, znajdujących się w katalogu <b>gallery</b>, na wartość <b>777</b>.
			</li>
			<li>
				Utworzyć na swoim serwerze bazę danych (pustą). Należy zapamiętać podane przy zakładaniu bazy parametry dostępu, tj. <b>host</b>, <b>user</b>, <b>password</b> oraz <b>baza</b>. 
			</li>
			<li>
				Wyedytować <b>plik konfiguracji</b> serwisu (config/config.php) - sekcję dot. połączenia z bazą danych, podając w niej parametry <b>host</b>, <b>user</b>, <b>password</b> oraz <b>baza</b> takie same, jak podczas tworzenia bazy.
			</li>
			<li>
				Uruchomić instalator serwisu, otwierając stronę <b>http://{domena_serwisu}/install</b>. Wprowadzić podstawowe informacje konfiguracyjne serwisu za pomocą formularza <b>Ustawienia</b>.
			</li>
			<li>
				Zalogować się na konto administratora, używając podanych w formularzu konfiguracyjnym <b>logina</b> oraz <b>hasła</b>, i rozpocząć zarządzanie serwisem.
			</li>
		</ol>
	';

$connection_content = 
	'
		<div class="intro">
			<h1>Połączenie z bazą danych nie powiodło się.</h1>
			<h2>Przyczyną może być zbyt duże obciążenie serwera.</h2>
			<h2>Spróbuj ponownie.</h2>
			
			<div class="form">
				<form action="'.$_SERVER["REQUEST_URI"].'" method="get">
					<input type="submit" value="Połącz ponownie" />
				</form>
			</div>
		</div>
	';
	
?>