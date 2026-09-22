@echo off
REM Atajo para Windows:  ia baseline | impacto | rutas | refs | verificar | limpiar
setlocal
if "%1"=="" goto ayuda
if /I "%1"=="baseline"  ( php scripts\baseline.php & goto fin )
if /I "%1"=="impacto"   ( php scripts\impacto.php %2 %3 & goto fin )
if /I "%1"=="rutas"     ( php scripts\comparar_rutas.php & goto fin )
if /I "%1"=="refs"      ( php scripts\verificar_referencias.php & goto fin )
if /I "%1"=="verificar" ( php scripts\verificar.php %2 & goto fin )
if /I "%1"=="limpiar"   ( php scripts\limpiar.php %2 & goto fin )
:ayuda
echo Uso: ia [baseline^|impacto^|rutas^|refs^|verificar^|limpiar]
echo   ia baseline              congela el contrato publico
echo   ia impacto               que queda afectado por tus cambios
echo   ia verificar             puerta de calidad completa
echo   ia limpiar --apply       mueve basura a cuarentena
:fin
endlocal
