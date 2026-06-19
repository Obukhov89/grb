# Инструкция по развертыванию Битрикс 24 + U-Lab

**Сервер на Arch Linux**

1. Скачайте установочный образ Arch с [https://archlinux.org/download/](https://archlinux.org/download/).
2. Установите его на флеш-носитель при помощи [Rufus](https://rufus.ie/ru/).
3. Зайдите в BIOS/UEFI и отключите Secure boot и Fast/Quick boot.
4. Выберите загрузку с флеш-носителя (через boot menu).
<p align=center>
<img src=png/1.png  width=900>
</p>

5. Осуществите загрузку с флеш-носителя.
<p align=center>
<img src=png/2.png  width=900>
</p>

6. Введите команду `# archinstall` 
<p align=center>
<img src=png/3.png  width=900>
</p>
В результате откроется меню:
<p align=center>
<img src=png/4.png  width=900>
</p>

В данном меню последовательно по пунктам:
7. Пропишите регион зеркал.
<p align=center>
<img src=png/5.png  width=900>
</p>

8. Выберите локаль: ru_RU
9. Выберите кодировку: UTF-8
10. Выберите жесткий диск
<p align=center>
<img src=png/7.png  width=900>
</p>

11. Выберите "Стереть всё". 
<p align=center>
<img src=png/8.png  width=900>
</p>

12. Выберите "Файловая система: ext4
<p align=center>
<img src=png/9.png  width=900>
</p>

13. Выберите home отдельно: нет
12. Выберите bootloader: GRUB
13. Заполните имя компьютера (hostname) (строчными буквами, в одно слово).
<p align=center>
<img src=png/10.png  width=900>
</p>

14. Заполните пароль root (должен быть сложным, где-то нужно его записать).
<p align=center>
<img src=png/12.png  width=900>
</p>

15. Добавьте пользователя: "имя: user", "пароль:<ваш_пароль>", "добавить в группу sudo: да"
<p align=center>
<img src=png/16.png  width=900>
</p>

16. Выберите ядро: linux-lts
<p align=center>
<img src=png/18.png  width=900>
</p>

17. Добавьте дополнительные пакеты, введя: `git nano mc wget htop openssh man-db apache mariadb zip unzip libreoffice-still libreoffice-still-ru jre-openjdk jdk-openjdk`
<p align=center>
<img src=png/19.png  width=900>
</p>

18. Установите конфигурацию сети: "скопировать данные из установочного образа".
<p align=center>
<img src=png/20.png  width=900>
</p>

19. Выберите местную временную зону.
<p align=center>
<img src=png/21.png  width=900>
</p>

20. Нажмите Установить (install)
<p align=center>
<img src=png/23.png  width=900>
</p>

21. После установки на вопрос "Открыть окружение chroot?" ответьте "да".
<p align=center>
<img src=png/26.png  width=900>
</p>

22. Введите команду `# nano /etc/vconsole.conf`
    > **Откроется файл vconsole.conf**
	Измените его содержимое на:
		FONT=cyr-sun16
		KEYMAP=ru
	Сохраните изменения, нажав Ctrl+S
	Выйдите из редактора, нажав Ctrl+X
<p align=center>
<img src=png/29.png  width=900>
</p>

23. Введите команду `# nano /etc/pacman.conf`
	> **Откроется файл pacman.conf**
	    Включите поиск, нажав Ctrl+W
	    Введите "ParallelDownloads" без кавычек 
        Нажмите Enter
	    Удалите символ "#" в начале строки
	    Сохраните изменения, нажав Ctrl+S
	    Выйдите из редактора, нажав Ctrl+X

24. Последовательно введите следующие команды:
    ```
    # mkdir /home/user/public_html
    # mariadb-install-db --user=mysql --basedir=/usr --datadir=/var/lib/mysql
    # systemctl enable httpd.service
    # systemctl enable sshd.service
    # systemctl enable mariadb.service
    # exit
    # reboot
    ```
25. После загрузки системы осуществите вход под логином user и паролем, который вы установили в шаге 15.

26. Для отображения IP введите `$ ip addr show`

27. На машине-клиенте введите `$ ssh user@<ip из шага 26>`
28.	Введите пароль из шага 15.

28. Установите  Yay. Для этого введите:
    ```
    $ git clone https://aur.archlinux.org/yay.git
    $ cd yay
    $ makepkg -sir
    Введите <password> из шага 15
    $ cd ..
    $ rm -rf yay
    ```
<p align=center>
<img src=png/40.png  width=900>
</p>
<p align=center>
<img src=png/41.png  width=900>
</p>
<p align=center>
<img src=png/42.png  width=900>
</p>
<p align=center>
<img src=png/43.png  width=900>
</p>
<p align=center>
<img src=png/44.png  width=900>
</p>

29. Установите php74 из AUR. Для этого:
    ```
    $ yay -S php74 php74-gd php74-cli php74-openssl php74-mysql php74-json php74-phar php74-iconv
	"удалить зависимости после сборки?": Y
	"показать изменения?": N
	"Введите номер репозитория": 1 (повторить несколько раз)
	Подтверждаем установку зависимостей
	ждем, пока скомпилируется
	Подтверждаем установку php74
    # ln -s /usr/bin/php74 /usr/bin/php
	<тут будут уточнения>
    ```

30. Изучите инструкцию https://getcomposer.org/download/, а затем выполните в SSH (хэш брать на странице getcomposer, он меняется от версии к версии):
    ```
    $ php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    $ php -r "if (hash_file('sha384', 'composer-setup.php') === '<*actual_hash*>') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
    $ php composer-setup.php
    $ php -r "unlink('composer-setup.php');"
    $ sudo mv composer.phar /usr/local/bin/composer
    ```

31. Сконфигурируйте apache, mariadb и php7 под ваши нужды (coming soon):
https://wiki.archlinux.org/title/MariaDB
https://wiki.archlinux.org/title/Apache_HTTP_Server
https://wiki.archlinux.org/title/PHP

32. Скачайте с сайта bitrix файл restore.php:
    ``` 
    $ cd ~/public_html
    $ wget https://www.1c-bitrix.ru/download/scripts/restore.php
    ```

33. На машине-клиенте откройте браузер
34. Перейдите по адресу http://[ip-адрес из пункта 27]/restore.php

35. Разверните сайт, следуя инструкциям на сайте bitrix:
[https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=32&CHAPTER_ID=02014](https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=32&CHAPTER_ID=02014)
Логин и пароль от базы данных возьмите из пункта 31

