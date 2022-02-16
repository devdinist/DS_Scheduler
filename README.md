# DS_Scheduler

## __이게 뭔가요?__
* ### __그누보드에서 사용하실 수 있는 스케쥴링 플러그인 입니다.__ 
* ### 개발에 사용된 그누보드 버전 : 5.4.18
<hr/>

## __설치 방법__
- ### 1. adm/schedule 폴더를 adm 폴더 내에 복사합니다.
- ### 2. adm/admin.menu600.php 파일을 adm 폴더 내에 복사합니다.<br>(만약 600번대 메뉴를 사용중이라면 다른 번호대로 변경해주셔야 합니다.)
- ### 3. extend/auto.schedule.php 파일을 extend 폴더 내에 복사합니다.
- ### 4. plugin/Crawler_Detect 폴더를 plugin 폴더 내에 복사합니다.<br> (Crawler_Detect는 검색 봇, 크롤러 감지를 위해 사용되었습니다.)
- ### 5. 복사가 완료되면 관리자 페이지에서 스케쥴관리 또는 스케쥴로그 메뉴에<br> 접근하시면 자동으로 DB 생성작업이 완료됩니다.

<hr/>

## __플러그인의 한계__
* ### __사용자 또는 봇이 페이지에 접근할때 작업이 이루어 지므로,<br> 페이지 접근이 없을경우 스케쥴이 실행되지 않습니다.__
* ### __이로 인해, 사용자 또는 봇의 페이지 접근 주기가 스케쥴링에 설정된 주기보다 길 경우 정상적인 주기대로 실행되지 않습니다.__
  
<hr/>

## __PHP 파일 검증 하지않기 (v1.0.3~)__
* ### 현재 스케쥴링 등록시에는 PHP 파일만 등록할 수 있게 되어있습니다. <br>만약 PHP 이외 파일도 스케쥴링이 가능하도록 하기 원하시면 다음 절차를 따르세요.

  1. 기존 adm/schedule/schedule_form_update.php 을 다른 이름으로 바꿉니다.<br> ex) schedule_form_update.php.bak
   
  2. adm/schedule/schedule_form_update.php.nonverify 파일을 <br> adm/schedule/schedule_form_update.php 로 변경합니다.
   
  3. 기존 extend/auto.schedule.php 을 다른 이름으로 바꿉니다.<br> ex) auto.schedule.php.bak
  
  4. extend/auto.schedule.php.nonverify 파일을 <br> extend/auto.schedule.php 로 변경합니다. 

* ### 만약 PHP 검증을 다시 하고싶으시면 위 절차와 같이 파일명을 다시 변경해 주시면됩니다. (다시 PHP 검증을 사용하시면 PHP 파일 이외에는  스케쥴을 실행하지 못합니다.)

<hr/>

## __실행 요일과 실행 시간 설정 (v1.0.9~)__
* ### 이 기능이 추가되면서 PHP의 intl 모듈이 필요하며, 활성화 되어 있어야 합니다.
* ### 이제 스케쥴링을 실행할 수 있는 시간과 요일을 설정할 수 있습니다.