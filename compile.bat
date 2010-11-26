@echo off
set adir="%cd%"
DEL de.wcf.tommygfx.business.tar
"%ProgramFiles%\7-Zip\7z.exe" a -r -ttar %adir%\files.tar %adir%\files\*
"%ProgramFiles%\7-Zip\7z.exe" a -r -ttar %adir%\templates.tar %adir%\templates\*
"%ProgramFiles%\7-Zip\7z.exe" a -r -ttar %adir%\acptemplates.tar %adir%\acptemplates\*
"%ProgramFiles%\7-Zip\7z.exe" a -r -ttar %adir%\pip.tar %adir%\pip\*
"%ProgramFiles%\7-Zip\7z.exe" a -r -ttar %adir%\de.wcf.tommygfx.business.tar @filelist.txt
"%ProgramFiles%\7-Zip\7z.exe" a -r -tgzip %adir%\de.wcf.tommygfx.business.tar.gz de.wcf.tommygfx.business.tar
DEL files.tar templates.tar acptemplates.tar pip.tar de.wcf.tommygfx.business.tar
