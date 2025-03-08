@props(['disabled' => false, 'type' => null, 'value' => null, 'min' => 1, 'max' => 1000, 'fieldName' => null])

<style>
    .form_control {
        color: #635a5a;
    }

    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        pointer-events: all;
        width: 16px;
        height: 16px;
        background-color: #fff;
        border-radius: 50%;
        box-shadow: 0 0 0 1px #C6C6C6;
        cursor: pointer;
    }

    input[type=range]::-moz-range-thumb {
        -webkit-appearance: none;
        pointer-events: all;
        width: 16px;
        height: 16px;
        background-color: #fff;
        border-radius: 50%;
        box-shadow: 0 0 0 1px #C6C6C6;
        cursor: pointer;
    }

    input[type=range]::-webkit-slider-thumb:hover {
        background: #f7f7f7;
    }

    input[type=range]::-webkit-slider-thumb:active {
        box-shadow: inset 0 0 3px #387bbe, 0 0 9px #387bbe;
        -webkit-box-shadow: inset 0 0 3px #387bbe, 0 0 9px #387bbe;
    }

    input[type="number"] {
        color: #8a8383;
        width: 50px;
        height: 30px;
        font-size: 20px;
        border: none;
    }

    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        opacity: 1;
    }

    input[type="range"] {
        -webkit-appearance: none;
        appearance: none;
        height: 2px;
        width: 100%;
        position: absolute;
        background-color: #C6C6C6;
        pointer-events: none;
    }

    #fromSlider {
        height: 0;
        z-index: 1;
    }
</style>

@if($type === 'select')
    <select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!}>
        <option class="text-sm" value="{{ null }}">{{ __('general.select_an_option') }}</option>
        {{ $options }}
    </select>
@elseif ($type === 'text-area')
     <textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!} ></textarea>
@elseif ($type === 'radio')
    <input x-init type="radio" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!} />
@elseif ($type === 'range')
    <div
        x-data="{
            min: {{ $min }},
            max: {{ $max }},
            controlFromSlider() {
                const [from, to] = this.getParsed($refs.fromSlider, $refs.toSlider);
                this.fillSlider($refs.fromSlider, $refs.toSlider, '#C6C6C6', '#25daa5', $refs.toSlider);
                if (from > to) {
                    $refs.fromSlider.value = to;
                    this.min = to;
                } else {
                    this.min = from;
                }

                $refs.finalValueInput.value = this.min + '-' + this.max;
            },
            controlToSlider() {
                const [from, to] = this.getParsed($refs.fromSlider, $refs.toSlider);
                this.fillSlider($refs.fromSlider, $refs.toSlider, '#C6C6C6', '#25daa5', $refs.toSlider);
                this.setToggleAccessible($refs.toSlider);
                if (from <= to) {
                    toSlider.value = to;
                    this.max = to;
                } else {
                    this.max = from;
                    toSlider.value = from;
                }

                $refs.finalValueInput.value = this.min + '-' + this.max;
            },
            getParsed(currentFrom, currentTo) {
                const from = parseInt(currentFrom.value, 10);
                const to = parseInt(currentTo.value, 10);
                return [from, to];
            },
            fillSlider(from, to, sliderColor, rangeColor, controlSlider) {
                const rangeDistance = to.max - to.min;
                const fromPosition = from.value - to.min;
                const toPosition = to.value - to.min;
                controlSlider.style.background = `linear-gradient(
                    to right,
                    ${sliderColor} 0%,
                    ${sliderColor} ${(fromPosition)/(rangeDistance)*100}%,
                    ${rangeColor} ${((fromPosition)/(rangeDistance))*100}%,
                    ${rangeColor} ${(toPosition)/(rangeDistance)*100}%,
                    ${sliderColor} ${(toPosition)/(rangeDistance)*100}%,
                    ${sliderColor} 100%)`;
            },
            setToggleAccessible(currentTarget) {
                const toSlider = document.querySelector('#toSlider');

                if (Number(currentTarget.value) <= 0 ) {
                    toSlider.style.zIndex = 2;
                } else {
                    toSlider.style.zIndex = 0;
                }
            },
            dispatchValue(event) {
                event.preventDefault();
                $refs.finalValueInput.dispatchEvent(new window.Event('change', { bubbles: true }));
            }
        }"
        x-init="() => {
            const fromSlider = document.querySelector('#fromSlider');
            const toSlider = document.querySelector('#toSlider');
            fillSlider($refs.fromSlider, $refs.toSlider, '#C6C6C6', '#25daa5', toSlider);
            setToggleAccessible($refs.toSlider);
        }"
        class="range_container w-full mt-6 mx-auto my-10 flex flex-col">
        <div class="sliders_control w-full relative">
            <input id="fromSlider" x-on:input="controlFromSlider" x-on:mouseup="dispatchValue" type="range" x-ref="fromSlider" value="{{ $min }}" min="{{ $min }}" max="{{ $max }}"/>
            <input id="toSlider" x-on:input="controlToSlider" x-on:mouseup="dispatchValue" type="range" x-ref="toSlider" value="{{ $max }}" min="{{ $min }}" max="{{ $max }}"/>
            <input x-ref="finalValueInput" type="hidden" {!! $attributes->merge([]) !!} />
        </div>
        <div class="form_control flex content-around w-40 mt-6 size-5 text-sm relative">
            <div class="form_control_container flex absolute top-1 left-0">
                <div class="form_control_container__time">
                    Min
                    <span x-text="min"></span>
                </div>
                <input class="form_control_container__time__input hidden" x-model="min" x-ref="minInput" type="number" value="{{ $min }}" min="{{ $min }}" name="{{ $fieldName }}-min" max="{{ $max }}"/>
            </div>
            <div class="form_control_container flex absolute top-1 right-0">
                <div class="form_control_container__time">
                    Max
                    <span x-text="max"></max>
                </div>
                <input class="form_control_container__time__input hidden" x-ref="maxInput" x-model="max" type="number" value="{{ $max }}" min="{{ $min }}" name="{{ $fieldName }}-max" max="{{ $max }}"/>
            </div>
        </div>
    </div>
@else
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!} />
@endif