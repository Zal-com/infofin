@props(['title', 'desc', 'button1', 'button2', 'url1', 'url2'])
<div class="border-2 my-5 p-5">
    <h2>{{$title}}</h2>
    @isset($desc)
        <p>{{$desc}}</p>
    @endisset
    <div class="my-2 flex justify-end space-x-5">
        <a href={{$url1}}>
            <button class="bg-blue-400 text-white py-2 px-4 rounded"><i
                    class="fa-sharp fa-solid fa-arrow-right m-2"></i>{{$button1}}
            </button>
        </a>
        @isset($url2, $button2)
            <a href={{$url2}}>
                <button class="bg-blue-400 text-white py-2 px-4 rounded"><i
                        class="fa-sharp fa-solid fa-arrow-right m-2"></i>{{$button2}}
                </button>
            </a>
        @endisset
    </div>
</div>
