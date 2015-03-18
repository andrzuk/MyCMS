<?php

$intro_content = 
	'
		Serwis nie został do końca zainstalowany lub skonfigurowany. Aby w pełni zainstalować serwis, proszę wykonać następujące kroki:
		<ol>
			<li>
				Za pomocą <b>FTP</b> wgrać skrypty aplikacji na swój serwer do katalogu <b>public_html</b>.
			</li>
			<li>
				Za pomocą <b>ChMod</b> zmienić atrybuty dla katalogów: <b>docs</b>, <b>images</b>, <b>sounds</b>, znajdujących się w katalogu <b>gallery</b>, na wartość <b>777</b>.
			</li>
			<li>
				Utworzyć na swoim serwerze bazę danych (pustą). Należy zapamiętać podane przy zakładaniu bazy parametry dostępu, tj. <b>host</b>, <b>user</b>, <b>password</b> oraz <b>baza</b>. Dla podanego usera przydzielić następujące <b>uprawnienia</b>: <ul><li><b>Dane</b> - <b>SELECT</b>, <b>INSERT</b>, <b>UPDATE</b>, <b>DELETE</b>,</li><li><b>Struktura</b> - <b>CREATE</b>, <b>ALTER</b>, <b>INDEX</b>, <b>DROP</b>.</li></ul>
			</li>
			<li>
				Wyedytować <b>plik konfiguracji</b> serwisu - sekcję dot. połączenia z bazą danych, podając w niej parametry <b>host</b>, <b>user</b>, <b>password</b> oraz <b>baza</b> takie same, jak podczas tworzenia bazy.
			</li>
			<li>
				Uruchomić instalator serwisu, otwierając stronę <b>http://{domena_serwisu}/install</b>. Wprowadzić podstawowe informacje konfiguracyjne serwisu za pomocą formularza <b>Ustawienia</b>. Formularz będzie widoczny tylko wtedy, gdy w pliku konfiguracji zostanie prawidłowo ustawione połączenie z bazą danych.
			</li>
			<li>
				Po wypełnieniu i zapisaniu formularza cofnąć uprawnienia usera dot. <b>Struktury</b>, tj. <b>CREATE</b>, <b>ALTER</b>, <b>INDEX</b>, <b>DROP</b>, oraz usunąć z serwera cały katalog <b>install</b>. Następnie przejść do strony głównej serwisu <b>http://{domena_serwisu}</b>.
			</li>
			<li>
				Zalogować się na konto administratora, używając podanych w formularzu konfiguracyjnym <b>logina</b> oraz <b>hasła</b>, i rozpocząć zarządzanie serwisem.
			</li>
		</ol>
	';

?>