async function ladeBenutzername() {
  const res = await fetch("get_user.php");
  if (res.ok) {
    const user = await res.json();
    document.getElementById("userDisplay").textContent = ` ${user.username}`;
  }
}

ladeBenutzername();

document.addEventListener("DOMContentLoaded", () => {
  ladeEvents();
});

const form = document.getElementById("eventForm");
const submitBtn = form.querySelector("button");
const eventIdField = document.getElementById("eventId");

form.addEventListener("submit", async function (e) {
  e.preventDefault();

  const formData = new FormData();
  const id = eventIdField.value;

  formData.append("datum", document.getElementById("datum").value);
  formData.append("uhrzeit", document.getElementById("uhrzeit").value);
  formData.append("ort", document.getElementById("ort").value);
  formData.append("beschreibung", document.getElementById("beschreibung").value);
  if (id) formData.append("id", id);

  await fetch("save_event.php", {
    method: "POST",
    body: formData
  });

  form.reset();
  eventIdField.value = "";
  submitBtn.textContent = "Speichern";
  ladeEvents();
});

async function ladeEvents() {
  const res = await fetch("get_events.php");
  const events = await res.json();

  const liste = document.getElementById("eventListe");
  liste.innerHTML = "";

  for (const event of events) {
    const div = document.createElement("div");
    div.className = "event";

    const invitedUserRes = await fetch(`get_invited_users.php?event_id=${event.id}`);
    const invitedUsers = invitedUserRes.ok ? await invitedUserRes.json() : [];

    let invitedUserList = invitedUsers.length
      ? `<div class="invited-users"><strong>Eingeladene:</strong><ul>` + invitedUsers.map(u => `<li>${u.username} ${u.invited_by ? `(eingeladen von ${u.invited_by})` : ""}</li>`).join("") + `</ul></div>`
      : "";

    div.innerHTML = `
      <strong>${event.datum} ${event.uhrzeit}</strong><br>
      <em>${event.ort}</em><br>
      <p>${event.beschreibung}</p>
      ${invitedUserList}
      <div class="invite-box">
        <input type="text" placeholder="Benutzer einladen..." class="inviteInput" data-id="${event.id}">
        <div class="suggestions"></div>
      </div>
      <div class="button-row">
        <button class="btn-bearbeiten" data-id="${event.id}">Bearbeiten</button>
        <button class="btn-loeschen" data-id="${event.id}">Löschen</button>
      </div>
    `;

    div.querySelector(".btn-bearbeiten").addEventListener("click", async () => {
      const res = await fetch(`get_event.php?id=${event.id}`);
      if (res.ok) {
        const daten = await res.json();
        document.getElementById("datum").value = daten.datum;
        document.getElementById("uhrzeit").value = daten.uhrzeit;
        document.getElementById("ort").value = daten.ort;
        document.getElementById("beschreibung").value = daten.beschreibung;
        document.getElementById("eventId").value = daten.id;
        submitBtn.textContent = "Aktualisieren";
      } else {
        alert("Fehler beim Laden des Events.");
      }
    });

    div.querySelector(".btn-loeschen").addEventListener("click", async () => {
      const confirmDelete = confirm("Willst du diesen Event wirklich löschen?");
      if (!confirmDelete) return;

      const res = await fetch(`delete_event.php?id=${event.id}`);
      if (res.ok) {
        ladeEvents();
      } else {
        alert("Löschen fehlgeschlagen.");
      }
    });

    const input = div.querySelector(".inviteInput");
    const suggestionsBox = input.nextElementSibling;

    input.addEventListener("input", async () => {
      const query = input.value.trim();
      if (query.length === 0) {
        suggestionsBox.innerHTML = "";
        return;
      }

      const res = await fetch(`search_users.php?q=${encodeURIComponent(query)}`);
      if (!res.ok) return;

      const usernames = await res.json();
      suggestionsBox.innerHTML = "";

      usernames.forEach(name => {
        const s = document.createElement("div");
        s.className = "suggestion";
        s.textContent = name;
        s.addEventListener("click", async () => {
          const formData = new FormData();
          formData.append("username", name);
          formData.append("event_id", input.dataset.id);

          const inviteRes = await fetch("invite_user.php", {
            method: "POST",
            body: formData
          });

          if (inviteRes.ok) {
            alert("Benutzer eingeladen!");
            input.value = "";
            suggestionsBox.innerHTML = "";
            ladeEvents();
          } else {
            alert(await inviteRes.text());
          }
        });

        suggestionsBox.appendChild(s);
      });
    });

    input.addEventListener("blur", () => {
      setTimeout(() => suggestionsBox.innerHTML = "", 200);
    });

    liste.appendChild(div);
  }

  const invitedRes = await fetch("get_invited_events.php");
  const invitedEvents = await invitedRes.json();

  const invitedListe = document.getElementById("invitedListe");
  invitedListe.innerHTML = "";

  invitedEvents.forEach(event => {
    const div = document.createElement("div");
    div.className = "event";
    div.innerHTML = `
      <strong>${event.datum} ${event.uhrzeit}</strong><br>
      <em>${event.ort}</em><br>
      <p>${event.beschreibung}</p>
      <div class="invited-by">Eingeladen von: ${event.invited_by ?? "Unbekannt"}</div>
    `;
    invitedListe.appendChild(div);
  });
}

// Responsiveness anpassen
document.querySelector("body").style.maxWidth = "100vw";
document.querySelector("body").style.overflowX = "hidden";