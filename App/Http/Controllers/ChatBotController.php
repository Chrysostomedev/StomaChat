  <!-- <?php  -->

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Log; -->

// class ChatBotController extends Controller
// {
//     public function index()
//     {
//         return view('chatbot');
//     }

//     public function sendMessage(Request $request)
//     {
//         $message = trim($request->input('message'));

//         if (empty($message)) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Message vide.'
//             ]);
//         }

//         $responseText = $this->getGeminiResponse($message);

//         return response()->json([
//             'status' => 'success',
//             'message' => $responseText
//         ]);
//     }

//     private function getGeminiResponse(string $message): string
//     {
//         $apiKey = env('GEMINI_API_KEY');

//         if (!$apiKey) {
//             return "Clé API Gemini non définie 😅";
//         }

//         // ⚠️ Utiliser un vrai tiret ASCII
//         $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent";

//         $payload = [
//             "contents" => [
//                 [
//                     "parts" => [
//                         ["text" => $message]
//                     ]
//                 ]
//             ]
//         ];

//         try {
//             $res = Http::withHeaders([
//                 'x-goog-api-key' => $apiKey,
//                 'Content-Type'   => 'application/json'
//             ])->post($endpoint, $payload);

//             if ($res->successful()) {
//                 $data = $res->json();

//                 // 🔹 Vérifie la structure exacte et récupère le texte
//                 $text = $data['candidates'][0]['content']['parts'][0] 
//                     ?? ($data['candidates'][0]['content']['parts'][0]['text'] ?? null);

//                 return $text ?? "Je n’ai pas compris 😅";
//             }

//             // Log complet pour debug
//             Log::error('Gemini API Error', [
//                 'status' => $res->status(),
//                 'body' => $res->body()
//             ]);

//             return "Erreur API 😅 – code " . $res->status();
//         } catch (\Exception $e) {
//             // Log détaillé
//             Log::error('Gemini Exception', ['message' => $e->getMessage()]);
//             return "Erreur serveur 😅";
//         }
//     }
// } -->
