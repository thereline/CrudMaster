// src/directives/resizeColumn.js
export default {
    mounted(el) {
        const resizer = document.createElement('div');
        resizer.classList.add('resizer');
        el.appendChild(resizer);
        resizer.addEventListener('mousedown', startDrag);

        function startDrag(e) {
            document.addEventListener('mousemove', drag);
            document.addEventListener('mouseup', stopDrag);

            function drag(event) {
                el.style.width = `${event.clientX - el.getBoundingClientRect().left}px`;
            }

            function stopDrag() {
                document.removeEventListener('mousemove', drag);
                document.removeEventListener('mouseup', stopDrag);
            }
        }
    }
};
