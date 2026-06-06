using System;
using System.IO;
using System.Net;
using System.Text;
using System.Threading;
using System.Windows.Forms;

class PrintAgent {
    static HttpListener listener;

    [STAThread]
    static void Main(string[] args) {
        Console.WriteLine();
        Console.WriteLine("=== Diego's Pizza - Agente de Impresion Silenciosa ===");
        Console.WriteLine("Usa WebBrowser.Print() - Sin dialogo, sin popups");
        Console.WriteLine();

        // Register URL prefix (may need admin on some systems)
        listener = new HttpListener();
        listener.Prefixes.Add("http://127.0.0.1:9199/");

        try {
            listener.Start();
        } catch (Exception ex) {
            Console.WriteLine("ERROR: No se pudo iniciar en puerto 9199");
            Console.WriteLine(ex.Message);
            Console.WriteLine();
            Console.WriteLine("Prueba ejecutando como Administrador una vez:");
            Console.WriteLine("  netsh http add urlacl url=http://127.0.0.1:9199/ user=everyone");
            Console.WriteLine();
            Pause();
            return;
        }

        Console.WriteLine("Escuchando en http://127.0.0.1:9199");
        Console.WriteLine("Impresora predeterminada: " + GetDefaultPrinter());
        Console.WriteLine("Presiona Ctrl+C para detener");
        Console.WriteLine();

        while (true) {
            try {
                var ctx = listener.GetContext();
                ThreadPool.QueueUserWorkItem(_ => ProcessRequest(ctx));
            } catch (Exception ex) {
                Console.WriteLine("Error: " + ex.Message);
            }
        }
    }

    static string GetDefaultPrinter() {
        try {
            var ps = new System.Drawing.Printing.PrinterSettings();
            return ps.PrinterName;
        } catch {
            return "(no detectada)";
        }
    }

    static void ProcessRequest(HttpListenerContext ctx) {
        var req = ctx.Request;
        var res = ctx.Response;

        res.Headers.Add("Access-Control-Allow-Origin", "*");
        res.Headers.Add("Access-Control-Allow-Methods", "POST, GET, OPTIONS");
        res.Headers.Add("Access-Control-Allow-Headers", "Content-Type");

        if (req.HttpMethod == "OPTIONS") {
            res.StatusCode = 204;
            res.Close();
            return;
        }

        if (req.HttpMethod == "GET" && req.Url.AbsolutePath == "/ping") {
            RespondJson(res, 200, "{\"ok\":true,\"printer\":\"" + EscapeJson(GetDefaultPrinter()) + "\"}");
            return;
        }

        if (req.HttpMethod == "POST" && req.Url.AbsolutePath == "/print") {
            try {
                string body;
                using (var reader = new StreamReader(req.InputStream)) {
                    body = reader.ReadToEnd();
                }

                var pedidoId = ExtractJsonValue(body, "pedido_id");
                var serverUrl = ExtractJsonValue(body, "server_url");

                if (pedidoId == null || serverUrl == null) {
                    RespondJson(res, 400, "{\"error\":\"Faltan pedido_id o server_url\"}");
                    return;
                }

                var ticketUrl = serverUrl.TrimEnd('/') + "/admin/ticket/" + pedidoId;
                Console.WriteLine("Imprimiendo ticket #" + pedidoId);

                var result = PrintUrl(ticketUrl);

                if (result.Success) {
                    Console.WriteLine("Ticket #" + pedidoId + " enviado a: " + GetDefaultPrinter());
                    RespondJson(res, 200, "{\"ok\":true,\"printer\":\"" + EscapeJson(GetDefaultPrinter()) + "\"}");
                } else {
                    Console.WriteLine("Error ticket #" + pedidoId + ": " + result.Error);
                    RespondJson(res, 500, "{\"error\":\"" + EscapeJson(result.Error) + "\"}");
                }
            } catch (Exception ex) {
                Console.WriteLine("Error: " + ex.Message);
                RespondJson(res, 500, "{\"error\":\"" + EscapeJson(ex.Message) + "\"}");
            }
            return;
        }

        res.StatusCode = 404;
        res.Close();
    }

    class PrintResult { public bool Success; public string Error; }

    static PrintResult PrintUrl(string url) {
        var result = new PrintResult();
        var done = new AutoResetEvent(false);

        var t = new Thread(() => {
            try {
                using (var form = new Form()) {
                    form.WindowState = FormWindowState.Minimized;
                    form.ShowInTaskbar = false;
                    form.Load += (s, e) => form.Hide();

                    using (var wb = new WebBrowser()) {
                        wb.ScriptErrorsSuppressed = true;
                        form.Controls.Add(wb);
                        wb.Dock = DockStyle.Fill;

                        var hasPrinted = false;

                        wb.DocumentCompleted += (s, e) => {
                            if (!hasPrinted && e.Url != null && e.Url.AbsoluteUri == wb.Url.AbsoluteUri) {
                                hasPrinted = true;
                                try {
                                    Thread.Sleep(400);
                                    wb.Print();
                                    result.Success = true;
                                } catch (Exception ex) {
                                    result.Error = ex.Message;
                                }
                                form.Close();
                                done.Set();
                            }
                        };

                        wb.Navigate(url);
                        Application.Run(form);
                    }
                }
            } catch (Exception ex) {
                result.Error = ex.Message;
                done.Set();
            }
        });
        t.SetApartmentState(ApartmentState.STA);
        t.Start();

        if (!done.WaitOne(30000)) {
            t.Abort();
            result.Error = "Timeout esperando impresion (30s)";
        }

        return result;
    }

    static void RespondJson(HttpListenerResponse res, int code, string json) {
        res.StatusCode = code;
        res.ContentType = "application/json";
        var data = Encoding.UTF8.GetBytes(json);
        res.OutputStream.Write(data, 0, data.Length);
        res.Close();
    }

    static string ExtractJsonValue(string json, string key) {
        var search = "\"" + key + "\":\"";
        var idx = json.IndexOf(search);
        if (idx < 0) return null;
        idx += search.Length;
        var end = json.IndexOf("\"", idx);
        if (end <= idx) return null;
        return json.Substring(idx, end - idx)
            .Replace("\\\"", "\"")
            .Replace("\\\\", "\\");
    }

    static string EscapeJson(string s) {
        if (s == null) return "";
        return s.Replace("\\", "\\\\")
                .Replace("\"", "\\\"")
                .Replace("\n", "\\n")
                .Replace("\r", "\\r");
    }

    static void Pause() {
        Console.Write("Presiona Enter para salir...");
        Console.ReadLine();
    }
}
