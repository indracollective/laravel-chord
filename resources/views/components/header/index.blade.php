<div x-data="{
    init() {
        window.onscroll = () => {
            this.pageOffset = window.pageYOffset
        }
    },
    pageOffset: window.pageYOffset,
    get sticky() {
        console.log(this.pageOffset);
        return this.pageOffset > 2
    }
}"
     class="bg-transparent top-0 left-0 z-40 w-full flex items-center group/menu text-white"
     :class="{
        'fixed z-[9999] transition bg-white bg-opacity-80 backdrop-blur-sm shadow-inner sticky': sticky,
        'absolute': !sticky,
     }">
    <div class="container">
        <div class="flex -mx-4 items-center justify-between relative">
            <div class="px-4 w-60">
                <a href="/" class="navbar-logo w-full block group-[.sticky]/menu:text-dark flex items-center"
                   :class="sticky ? 'py-2' : 'py-5'">
                    <span
                        class="inline-block mr-2 bg-primary text-white text-3xl w-10 h-10 rounded-full flex items-center justify-center">
                        <span>C</span>
                    </span>
                    <span>CHORD</span>
                </a>
            </div>
            <div class="flex px-4 justify-between items-center w-full">
                <div>
                    <button id="navbarToggler"
                            class="block absolute right-4 top-1/2 -translate-y-1/2 lg:hidden focus:ring-2 ring-primary px-3 py-[6px] rounded-lg">
                        <span
                            class="relative w-[30px] h-[2px] my-[6px] block bg-white group-[.sticky]/menu:bg-dark"></span>
                        <span
                            class="relative w-[30px] h-[2px] my-[6px] block bg-white group-[.sticky]/menu:bg-dark"></span>
                        <span
                            class="relative w-[30px] h-[2px] my-[6px] block bg-white group-[.sticky]/menu:bg-dark"></span>
                    </button>
                    <nav id="navbarCollapse"
                         class="absolute py-5 lg:py-0 lg:px-4 xl:px-6 bg-white lg:bg-transparent shadow-lg rounded-lg max-w-[250px] w-full lg:max-w-full lg:w-full right-4 top-full hidden lg:block lg:static lg:shadow-none">
                        <ul class="block lg:flex">
                            @foreach($pagesForMenu('header') as $page)
                                <x-chord::component component="header.menu-item" :page="$page" />
                            @endforeach
                        </ul>
                    </nav>
                </div>
                <div class="sm:flex justify-end hidden pr-16 lg:pr-0">
                    <a href=/" class="text-base font-medium text-white hover:opacity-70 py-3 px-7 loginBtn">Sign
                        In
                    </a>
                    <a href="signup.html"
                       class="text-base font-medium text-white bg-white bg-opacity-20 rounded-lg py-3 px-6 hover:bg-opacity-100 hover:text-dark signUpBtn duration-300 ease-in-out">Sign
                        Up</a></div>
            </div>
        </div>
    </div>
</div>
