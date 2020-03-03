using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Security.Cryptography;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;
using System.Diagnostics;

namespace TrinitySeal
{
    class SealCheck
    {
        public static bool isValidDLL = false;
        public static void HashChecks()
        {
            if (CalculateMD5("Newtonsoft.Json.dll") != "4df6c8781e70c3a4912b5be796e6d337" || CalculateMD5(typeof(TrinitySeal.Seal).Assembly.Location) != "0ac32b36f97427f59bd8e179c2594d95")
            {
                MessageBox.Show("Hash check failed! Exiting...", "TrinitySeal", MessageBoxButtons.OK, MessageBoxIcon.Error);
                Process.GetCurrentProcess().Kill();
            }
            else
            {
                isValidDLL = true;
            }
        }

        private static string CalculateMD5(string filename)
        {
            using (var md5 = MD5.Create())
            {
                using (var stream = File.OpenRead(filename))
                {
                    var hash = md5.ComputeHash(stream);
                    return BitConverter.ToString(hash).Replace("-", "").ToLowerInvariant();
                }
            }
        }
    }
}