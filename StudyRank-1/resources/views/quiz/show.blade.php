{{--
    Variável esperada do QuizController@show:
    - $quiz → instância de Quiz (com $quiz->questions já como array via cast)
--}}

{{--
    Esta view é STANDALONE (não usa layouts/app) porque durante o quiz
    não queremos a navbar distraindo — igual ao que você vê no protótipo.
--}}
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $quiz->title }} - StudyRank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --purple: #a020f0; }

        body {
            min-height: 100vh;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', Arial, sans-serif;
            padding: 20px;
        }

        .quiz-container {
            background: white;
            border-radius: 20px;
            padding: 35px 40px;
            max-width: 680px;
            width: 100%;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }

        /* ── Barra de progresso das questões ── */
        .progress { height: 8px; border-radius: 10px; background: #eee; margin-bottom: 30px; }
        .progress-bar { background: linear-gradient(90deg, #a020f0, #0d6efd); transition: width 0.4s ease; }

        /* ── Navegação topo ── */
        .quiz-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            font-size: 0.85rem;
            color: #888;
        }
        .btn-voltar {
            background: none;
            border: none;
            color: #888;
            font-size: 0.85rem;
            cursor: pointer;
            padding: 0;
            text-decoration: none;
        }
        .btn-voltar:hover { color: var(--purple); }

        /* ── Texto da questão ── */
        .question-text {
            font-size: 1.15rem;
            font-weight: 700;
            color: #222;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        /* ── Opções de resposta ── */
        .option-label {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border: 1.5px solid #e5e5e5;
            border-radius: 12px;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }
        .option-label:hover { border-color: var(--purple); background: #fdf4ff; }
        .option-label input[type="radio"] { display: none; } {{-- Escondemos o radio nativo --}}

        /* Círculo customizado do radio --}}  */
        .option-circle {
            width: 20px; height: 20px;
            border: 2px solid #ccc;
            border-radius: 50%;
            flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            transition: border-color 0.2s;
        }
        /* Estado selecionado — via JS adicionamos a classe .selected no label */
        .option-label.selected {
            border-color: var(--purple);
            background: #fdf4ff;
        }
        .option-label.selected .option-circle {
            border-color: var(--purple);
            background: var(--purple);
        }
        .option-label.selected .option-circle::after {
            content: '';
            width: 8px; height: 8px;
            background: white;
            border-radius: 50%;
        }

        /* ── Botão próxima/finalizar ── */
        .btn-next {
            background: var(--purple);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .btn-next:disabled { opacity: 0.4; cursor: not-allowed; }
        .btn-next:not(:disabled):hover { opacity: 0.88; }
    </style>
</head>
<body>

<div class="quiz-container">

    {{-- ── Navegação superior ── --}}
    <div class="quiz-nav">
        <a href="{{ route('dashboard') }}" class="btn-voltar">← Voltar</a>
        <span id="question-counter">Questão 1 de {{ count($quiz->questions) }}</span>
    </div>

    {{-- ── Barra de progresso ── --}}
    <div class="progress">
        <div class="progress-bar" id="progress-bar" style="width: 0%"></div>
    </div>

    {{--
        O form aponta para QuizController@submit.
        Método POST + @csrf + Route Model Binding pelo ID do quiz.
        As respostas são enviadas como answers[0], answers[1], etc.
    --}}
    <form action="{{ route('quiz.submit', $quiz->id) }}" method="POST" id="quiz-form">
        @csrf

        {{--
            Campos hidden que o JavaScript preenche conforme o usuário responde.
            Criamos um campo para cada questão — o valor começa vazio e é
            preenchido quando o usuário clica numa opção.
        --}}
        @foreach($quiz->questions as $i => $q)
            <input type="hidden" name="answers[{{ $i }}]" id="answer-{{ $i }}" value="">
        @endforeach

        {{-- ── Painel de cada questão ── --}}
        @foreach($quiz->questions as $i => $question)
        <div class="question-panel" id="panel-{{ $i }}" style="{{ $i > 0 ? 'display:none;' : '' }}">

            <div class="question-text">{{ $question['text'] }}</div>

            {{-- Opções de múltipla escolha --}}
            @foreach($question['options'] as $j => $option)
            <label class="option-label"
                   onclick="selectOption({{ $i }}, {{ $j }}, this)">
                <input type="radio" name="q{{ $i }}" value="{{ $j }}">
                <span class="option-circle"></span>
                <span>{{ $option }}</span>
            </label>
            @endforeach

        </div>
        @endforeach

        {{-- ── Botão próxima / finalizar ── --}}
        <div class="d-flex justify-content-end mt-4">
            <button type="button"
                    id="btn-next"
                    class="btn-next"
                    disabled
                    onclick="nextQuestion()">
                Próxima →
            </button>
        </div>

    </form>
</div>

<script>
    // ── Estado do quiz ──
    const totalQuestions = {{ count($quiz->questions) }};
    let currentQuestion  = 0;
    let answered         = new Array(totalQuestions).fill(false); // rastrea quais foram respondidas

    /**
     * Chamado quando o usuário clica numa opção.
     * - Desmarca todas as opções do painel atual
     * - Marca a clicada
     * - Preenche o campo hidden com o índice da opção escolhida
     * - Habilita o botão "Próxima"
     */
    function selectOption(questionIndex, optionIndex, clickedLabel) {
        // Remove .selected de todas as opções desta questão
        const panel = document.getElementById('panel-' + questionIndex);
        panel.querySelectorAll('.option-label').forEach(l => l.classList.remove('selected'));

        // Marca a opção clicada
        clickedLabel.classList.add('selected');

        // Preenche o campo hidden com o índice da opção (o controller usa isso)
        document.getElementById('answer-' + questionIndex).value = optionIndex;

        // Marca esta questão como respondida e habilita o botão
        answered[questionIndex] = true;
        document.getElementById('btn-next').disabled = false;
    }

    /**
     * Avança para a próxima questão ou submete o form na última.
     */
    function nextQuestion() {
        const panel = document.getElementById('panel-' + currentQuestion);
        panel.style.display = 'none';

        currentQuestion++;

        if (currentQuestion >= totalQuestions) {
            // Chegou na última → submete o formulário
            document.getElementById('quiz-form').submit();
            return;
        }

        // Mostra o próximo painel
        document.getElementById('panel-' + currentQuestion).style.display = 'block';

        // Atualiza o contador e a barra de progresso
        document.getElementById('question-counter').textContent =
            'Questão ' + (currentQuestion + 1) + ' de ' + totalQuestions;

        const pct = (currentQuestion / totalQuestions) * 100;
        document.getElementById('progress-bar').style.width = pct + '%';

        // Desabilita o botão até o usuário responder a nova questão
        // (a menos que já tenha respondido antes — ao navegar para frente)
        document.getElementById('btn-next').disabled = !answered[currentQuestion];

        // Atualiza texto do botão na última questão
        if (currentQuestion === totalQuestions - 1) {
            document.getElementById('btn-next').textContent = 'Finalizar ✓';
        }
    }
</script>

</body>
</html>
