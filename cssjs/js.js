/*JS*/

 function loadTbl() {
	fetch('send.php')
		.then(response => response.json())
		.then(data => {
			const credits = data;
			const table = document.getElementById('tabl');
			let tableHTML = '<tr><th>Код</th><th>Име</th><th>Сума</th><th>Срок</th><th>Месечна вноска</th></tr>';

			credits.forEach(credit => {
				const { code, name, sum, srok, msum } = credit;
					tableHTML += `<tr><td>${code}</td><td>${name}</td><td>${sum}</td><td>${srok}</td><td>${msum}</td></tr>`;
                });
                table.innerHTML = tableHTML;
            })
            .catch(error => console.error('Error:', error));
}
 
 function showmsg (style, text){			
	var res = document.getElementById("res");
	res.innerText = text;
	res.classList.remove("error");
	res.classList.remove("ok");
	res.classList.add(style); 
					
	res.classList.remove("fadeOutAnimation");
	res.classList.add("fadeInAnimation"); 
	setTimeout(function() {
		res.classList.remove("fadeInAnimation");
		res.classList.add("fadeOutAnimation"); 
	}, 5000); 
}

function submitForm(event) {
	event.preventDefault(); 
	var form = document.getElementById("new");
	var formData = new FormData(form);
	var serializedData = new URLSearchParams(formData).toString();
	var options = {
		method: "POST",
			headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
			},
			body: serializedData
		};
	fetch("send.php", options)
		.then(response => response.json()) 
        .then(data => {
			if(data.ok==0){
				showmsg ("error", data.msg)
			}
			if(data.ok==1){
				showmsg ("ok", data.msg)
			}
		})
		.catch(error => {
		console.error("Error:", error);
	});
}

function checkInfo() {
	var inputValue = document.getElementById("no").value;
	var inputS = document.getElementById("s");
	var options = {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded'
			},
			body: 'action=check&no=' + encodeURIComponent(inputValue)
		};
		fetch('send.php', options)
			.then(response => response.json())
			.then(data => {
				if(data.ok==0){
					showmsg ("error", data.msg)
				}
				if(data.ok==1){
					console.log(data);
						//var months=data.credit.months;
						showmsg ("ok", "Кредит на "+data.credit.name+" за "+data.credit.suma+"лв. с вноска от "+data.credit.vnoska+"лв.");
						inputS.value=data.credit.vnoska;
				}
			})
			.catch(error => {
				console.error('Error:', error);
			});
}
			