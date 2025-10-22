<div>
    <label for="{{ $name }}">{{ $label ?? $name }}</label>

    <select id="{{ $name }}" name="{{ $name . ($multiple ? '[]' : '') }}"
        {{ $attributes->merge([
            'class' => 'form-control select2-' . str_replace(['[', ']'], '', $name) . ' ' . $validClass,
        ]) }}
        style="width: 100%;" data-dependent="{{ $dependent }}" data-cascade-value="{{ $dependentValue }}"
        data-disable-on-empty="{{ $disableOnEmptyParent ? '1' : '0' }}"
        @if ($multiple) multiple="multiple" @endif>

        @foreach ($selectedOptions as $option)
            <option value="{{ $option['id'] }}" selected>
                {{ $option['text'] }}
            </option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // --- 1. CONFIGURAÇÃO INICIAL ---
            let $selectElement = $('.select2-{{ str_replace(['[', ']'], '', $name) }}');
            let dependentOn = $selectElement.data(
                'dependent'); // O ID do campo PAI (ex: 'category_id') ou nulo se não houver pai
            let currentCascadeValue = $selectElement.data(
                'cascade-value'); // O VALOR INICIAL do pai (vem da rota /editar)
            let baseUrl = "{{ route('select2.list', ['model' => $model]) }}";
            let disableOnEmpty = $selectElement.data('disable-on-empty') ==
                1; // A flag que diz se o campo deve ser desabilitado

            // --- 2. FUNÇÃO PRINCIPAL DE INICIALIZAÇÃO ---
            // Esta função (re)inicializa o Select2 neste elemento.
            // Ela é chamada na carga da página e toda vez que o PAI muda.
            function initializeSelect2(cascadeValue) {

                currentCascadeValue = cascadeValue;
                // --- LÓGICA DA FLAG ---
                let isDisabled = false; // Por padrão, o campo é desabilitado
                if (disableOnEmpty) {
                    // Se a flag for true, verificamos se temos um pai (dependentOn)
                    // E se o valor desse pai está vazio (currentCascadeValue é nulo/undefined)
                    isDisabled = dependentOn && !currentCascadeValue;
                }
                // Aplica o estado (habilitado/desabilitado) ao <select>
                $selectElement.prop('disabled', isDisabled);
                // --- FIM DA LÓGICA ---

                if ($selectElement.data('select2')) {
                    $selectElement.select2('destroy');
                }

                $selectElement.select2({
                    placeholder: "{{ $placeholder }}",
                    allowClear: true,
                    theme: 'bootstrap4',
                    language: 'pt-BR',
                    ajax: {
                        url: baseUrl,
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term || '',
                                page: params.page,
                                cascade: currentCascadeValue
                            };
                        },
                        // Esta função processa a resposta JSON do Laravel
                        processResults: function(data, params) {
                            params.page = params.page || 1;

                            // Mapeia os dados do resource ('id', 'name') para o formato ('id', 'text')
                            // que o Select2 entende.
                            if (data.data && Array.isArray(data.data)) {
                                return {
                                    results: data.data.map(function(item) {
                                        return {
                                            id: item.id,
                                            text: item.name
                                        };
                                    }),
                                    // Informa ao Select2 se há mais páginas para carregar
                                    pagination: {
                                        more: data.meta.current_page < data.meta.last_page
                                    }
                                };
                            }
                            return {
                                results: []
                            }; // Retorno em caso de falha
                        },
                        cache: true
                    }
                });
            }

            // --- 3. OBSERVADORES (EVENT LISTENERS) ---
            // Este bloco só é executado se o campo for um "FILHO" (tiver um 'dependentOn')
            if (dependentOn) {
                let $dependentElement = $('#' + dependentOn); // Encontra o elemento PAI no HTML pelo ID

                // Observador 1: Ocorre quando o usuário SELECIONA um item no PAI
                $dependentElement.on('select2:select', function(e) {
                    let newCascadeValue = e.params.data.id; // Pega o ID do item que foi selecionado no PAI
                    $selectElement.empty().trigger(
                        'change'); // Limpa o select FILHO (remove seleção atual e opções)

                    initializeSelect2(newCascadeValue); // Reinicializa o FILHO com o novo ID do PAI
                });

                // Observador 2: Ocorre quando o usuário LIMPA a seleção do PAI
                $dependentElement.on('select2:unselect', function() {
                    $selectElement.empty().trigger('change'); // Limpa o select FILHO
                    initializeSelect2(null); // Reinicializa o FILHO sem valor de cascata (nulo)
                });
            }

            // --- 4. CHAMADA INICIAL ---
            initializeSelect2(currentCascadeValue);
        });
    </script>
@endpush
