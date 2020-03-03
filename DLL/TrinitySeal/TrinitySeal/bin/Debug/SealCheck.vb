Imports System
Imports System.Collections.Generic
Imports System.IO
Imports System.Linq
Imports System.Security.Cryptography
Imports System.Text
Imports System.Threading.Tasks
Imports System.Windows.Forms
Imports System.Diagnostics

Namespace Seal
    Class SealCheck
        Public Shared isValidDLL As Boolean = False

        Public Shared Sub HashChecks()
            If CalculateMD5("Newtonsoft.Json.dll") <> "4df6c8781e70c3a4912b5be796e6d337" OrElse CalculateMD5(GetType(TrinitySeal.Seal).Assembly.Location) <> "0ac32b36f97427f59bd8e179c2594d95" Then
                MessageBox.Show("Hash check failed. Exiting...", "TrinitySeal", MessageBoxButtons.OK, MessageBoxIcon.[Error])
                Process.GetCurrentProcess().Kill()
            Else
                isValidDLL = True
            End If
        End Sub

        Private Shared Function CalculateMD5(ByVal filename As String) As String
            Using md = MD5.Create()

                Using stream = File.OpenRead(filename)
                    Dim hash = md.ComputeHash(stream)
                    Return BitConverter.ToString(hash).Replace("-", "").ToLowerInvariant()
                End Using
            End Using
        End Function
    End Class
End Namespace
