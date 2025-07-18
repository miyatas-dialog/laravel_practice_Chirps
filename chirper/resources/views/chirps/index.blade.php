<x-app-layout>
    <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
        <form method="POST" action="{{ route('chirps.store') }}">
            @csrf
            <textarea
                name="message"
                placeholder="{{ __('What\'s on your mind?') }}"
                class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
            >{{ old('message') }}</textarea>
            <x-input-error :messages="$errors->get('message')" class="mt-2" />
            <x-primary-button class="mt-4">{{ __('Chirp') }}</x-primary-button>
        </form>
        <div class="mt-6 bg-white shadow-sm rounded-lg divide-y">
            @foreach ($chirps as $chirp)
                <div class="p-6 flex space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 -scale-x-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <div>
                                @if ($chirp->user->is(auth()->user()))
                                    <span class="text-gray-800">{{ $chirp->user->name }}</span>
                                @else
                                    <x-dropdown class="follow-menu">
                                        <x-slot name="trigger" >
                                            <button class="text-blue-400" >{{ $chirp->user->name }}</button>
                                        </x-slot>
                                        <x-slot name="content">
                                            <x-dropdown-link
                                                href="#" 
                                                class="follow-button"
                                                data-user-id="{{ $chirp->user->id }}"
                                            >
                                                @if (auth()->user()->isFollowing($chirp->user))
                                                    {{ __('既にフォロー中です') }}
                                                @else
                                                    {{ __('フォロー') }}
                                                @endif
                                            </x-dropdown-link>
                                            <x-dropdown-link
                                                href="#" 
                                                class="unfollow-button"
                                                data-user-id="{{ $chirp->user->id }}"
                                            >
                                                @if (auth()->user()->isFollowing($chirp->user))
                                                    {{ __('フォロー解除') }}
                                                @else
                                                    {{ __('フォローしていません') }}
                                                @endif
                                            </x-dropdown-link>
                                        </x-slot>
                                    </x-dropdown>
                                @endif
                                <small class="ml-2 text-sm text-gray-600">{{ $chirp->created_at->format('j M Y, g:i a') }}</small>
                                @unless ($chirp->created_at->eq($chirp->updated_at))
                                    <small class="text-sm text-gray-600"> &middot; {{ __('edited') }}</small>
                                @endunless
                            </div>
                            @if ($chirp->user->is(auth()->user()))
                                <x-dropdown>
                                    <x-slot name="trigger">
                                        <button>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                            </svg>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('chirps.edit', $chirp)">
                                            {{ __('Edit') }}
                                        </x-dropdown-link>
                                        <form method="POST" action="{{ route('chirps.destroy', $chirp) }}">
                                            @csrf
                                            @method('delete')
                                            <x-dropdown-link :href="route('chirps.destroy', $chirp)" onclick="event.preventDefault(); this.closest('form').submit();">
                                                {{ __('Delete') }}
                                            </x-dropdown-link>
                                        </form>
                                    </x-slot>
                                </x-dropdown>
                            @endif
                        </div>
                        <p class="mt-4 text-lg text-gray-900">{{ $chirp->message }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const followButtons = document.querySelectorAll('.follow-button');
            const unfollowButtons =document.querySelectorAll('.unfollow-button');

            followButtons.forEach(button => {
                button.addEventListener('click', async function(event) {
                    event.preventDefault();

                    //ボタンの情報を取得
                    const userId = this.dataset.userId;

                    //ボタンを無効化（連打防止）
                    this.style.pointerEvents = 'none';
                    this.style.opacity = '0.5';

                    try{
                        //APIリクエストを送信
                        const response = await axios.post(`/users/${userId}/follow`);

                        const data = response.data;

                        if(data.success){
                            //ボタンのテキストを「既にフォロー中です」に更新
                            this.textContent = '既にフォロー中です';
                            this.classList.add('text-gray-500');

                            //成功メッセージを表示（コンソールに出力）
                            console.log('フォロー成功:', data.message);

                            const unfollowButton = this.closest('.follow-menu').querySelector('.unfollow-button');
                            if(unfollowButton.textContent === 'フォローしていません'){
                                unfollowButton.textContent = 'フォロー解除';
                            }
                            

                        }
                    } catch(error) {
                        if(error.response) {
                            //サーバーからのエラーレスポンス
                            const data = error.response.data;
                            console.error('フォローエラー:', data.message);
                            alert('エラー: ' + (data.message || 'フォローに失敗しました'));
                        } else {
                            //ネットワークエラーなど
                            console.error('フォローエラー:', error);
                            alert('通信エラーが発生しました');
                        }
                    } finally {
                        //ボタンを再度有効化
                        this.style.pointerEvents = 'auto';
                        this.style.opacity = '1';
                    }
                });
            });

            unfollowButtons.forEach(button => {
                button.addEventListener('click', async function(event) {
                    event.preventDefault();

                    //ボタンの情報を取得
                    const userId = this.dataset.userId;

                    //ボタンを無効化（連打防止）
                    this.style.pointerEvents = 'none';
                    this.style.opacity = '0.5';

                    try{
                        //APIリクエストを送信
                        const response = await axios.post(`/users/${userId}/unfollow`);

                        const data = response.data;

                        if(data.success){
                            //ボタンのテキストを「フォローしていません」に更新
                            this.textContent = 'フォローしていません';
                            this.classList.add('text-gray-500');

                            //成功メッセージを表示（コンソールに出力）
                            console.log('フォロー解除成功:', data.message);

                            const followButton = this.closest('.follow-menu').querySelector('.follow-button');
                            if(followButton.textContent === '既にフォロー中です'){
                                followButton.textContent = 'フォロー';
                            }
                        }
                    } catch(error) {
                        if(error.response) {
                            //サーバーからのエラーレスポンス
                            const data = error.response.data;
                            console.error('フォロー解除エラー:', data.message);
                            alert('エラー: ' + (data.message || 'フォロー解除に失敗しました'));
                        } else {
                            //ネットワークエラーなど
                            console.error('フォロー解除エラー:', error);
                            alert('通信エラーが発生しました');
                        }
                    } finally {
                        //ボタンを再度有効化
                        this.style.pointerEvents = 'auto';
                        this.style.opacity = '1';
                    }
                });
            });

        });
    </script>
    @endpush
</x-app-layout>
