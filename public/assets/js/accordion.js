const accordionButtons = document.querySelectorAll(".accordion-open");

accordionButtons.forEach((button) => {
    button.addEventListener("click", () => {
        const accordionContent = button.nextElementSibling;
        const icon = button.querySelector("i");

        if (!accordionContent || !icon) {
            return;
        }

        const isOpen = accordionContent.classList.contains("grid-rows-[1fr]");

        if (isOpen) {
            accordionContent.classList.remove("grid-rows-[1fr]", "opacity-100", "mt-5", "mb-5","w-full");

            accordionContent.classList.add("grid-rows-[0fr]", "opacity-0", "mt-0","mb-0","w-0");

            icon.classList.remove("rotate-180");
            icon.classList.add("fa-plus");
            icon.classList.remove("fa-minus");
        } else {
            accordionContent.classList.remove("grid-rows-[0fr]", "opacity-0", "mt-0", "mb-0","w-0");

            accordionContent.classList.add("grid-rows-[1fr]", "opacity-100", "mt-5","mb-5","w-full");

            icon.classList.add("rotate-180");

            icon.classList.remove("fa-plus");
            icon.classList.add("fa-minus");
        }
    });
});
