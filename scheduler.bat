@echo off
echo Running daily scheduler...

php artisan app:check-tunggakan-spp

echo Daily scheduler run successfully..

timeout 10
exit
