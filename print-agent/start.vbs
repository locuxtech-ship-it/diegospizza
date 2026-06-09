' Inicia el agente de impresion sin ventana de consola
Dim shell, scriptDir
Set shell = CreateObject("Wscript.Shell")
scriptDir = CreateObject("Scripting.FileSystemObject").GetFile(Wscript.ScriptFullName).ParentFolder.Path
shell.Run "node """ & scriptDir & "\agent.js""", 0, False
Set shell = Nothing
