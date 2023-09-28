// import Glide from '@glidejs/glide'

// var rows = document.querySelectorAll('.team-row')
// var sliders = document.querySelectorAll('#team-sliders');
// if(sliders){
//     for (var i = 0; i < sliders.length; i++) {
//         if(rows[i].style.display !== 'none'){
//             var glide = new Glide(sliders[i], {
//                 type: 'slider',
//                 perView: 8,
//                 // focusAt: 'center',
//                 autoplay: 30000 + Math.floor(Math.random() * 5000),
//                 rewind: false,
//                 breakpoints: {
//                     2000:{
//                         perView: 7,
//                     },
//                     1700:{
//                         perView: 5,
//                     },
//                     1400:{
//                         perView: 5,
//                     },
//                     1100:{
//                         perView: 3,
//                     },
//                     800: {
//                         perView: 3
//                     },
//                     480: {
//                         perView: 1
//                     }
//                 }
//             })
//             glide.mount()
//         }
        
//     }
// }

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const locationInput = document.getElementById('location');
    const departmentInput = document.getElementById('department');
    const skillsInput = document.getElementById('skill');
    const noResultsMessage = document.getElementById('results-message');
    const filterForm = document.getElementById('filter-form');
    const searchForm = document.getElementById('search-form');
    const resetButton = document.getElementById('reset-button');

    const filters = {
        location: '',
        department: '',
        skills: '',
    };

    function filterMembers() {
        const searchValue = searchInput.value.toLowerCase();
        const teamRows = document.querySelectorAll('.team-row');
        let allRowsHidden = true;

        teamRows.forEach(row => {
            const teamMembers = row.querySelectorAll('.team-member');
            let allHidden = true;
            const department = row.querySelector('.team-name').textContent.toLowerCase();

            teamMembers.forEach(member => {
                const nickname = member.querySelector('.name').textContent.toLowerCase();
                const location = member.querySelector('.job').textContent.toLowerCase();
                const skill = member.querySelector('.skills').textContent.toLowerCase();

                const matchesSearch = nickname.includes(searchValue);
                const matchesLocation = filters.location === '' || filters.location === 'all' || location.includes(filters.location);
                const matchesDepartment = filters.department === '' || filters.department === 'all' || department.includes(filters.department);
                const matchesSkills = filters.skills === '' || skill.includes(filters.skills);

                if (matchesSearch && matchesLocation && matchesDepartment && matchesSkills) {
                    member.style.display = 'block';
                    allHidden = false;
                } else {
                    member.style.display = 'none';
                }
            });

            if (allHidden) {
                row.style.display = 'none';
            } else {
                row.style.display = '';
                allRowsHidden = false;
            }
        });

        if (allRowsHidden) {
            noResultsMessage.style.display = 'block';
        } else {
            noResultsMessage.style.display = 'none';
        }
    }

    // Event listeners for input fields
    searchInput.addEventListener('input', filterMembers);
    locationInput.addEventListener('input', function () {
        filters.location = locationInput.value.toLowerCase();
        if (filters.location.trim() === '') {
            filters.location = '';
        }
        filterMembers();
    });
    departmentInput.addEventListener('input', function () {
        filters.department = departmentInput.value.toLowerCase();
        if (filters.department.trim() === '') {
            filters.department = '';
        }
        filterMembers();
    });
    skillsInput.addEventListener('input', function () {
        filters.skills = skillsInput.value.toLowerCase();
        if (filters.skills.trim() === '') {
            filters.skills = '';
        }
        filterMembers();
    });

    resetButton.addEventListener('click', function () {
        filterForm.reset();
        searchForm.reset();
        filters.location = '';
        filters.department = '';
        filters.skills = '';
        filterMembers();
    });

    filterForm.addEventListener('submit', function (event) {
        event.preventDefault();
    });

    searchForm.addEventListener('submit', function (event) {
        event.preventDefault();
    });
});


