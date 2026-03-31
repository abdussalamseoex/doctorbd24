function getAiContext() {
    let context = {
        language: document.querySelector('select[name="language"]') ? document.querySelector('select[name="language"]').value : 'English',
        name: document.querySelector('input[name="name"], input[name="title"], input[name="provider_name"]') ? document.querySelector('input[name="name"], input[name="title"], input[name="provider_name"]').value : ''
    };

    let keywordInput = document.querySelector('input[name="keyword"]');
    if (keywordInput) context.keyword = keywordInput.value;

    let titleInput = document.querySelector('input[name="title"]');
    if (titleInput) context.title = titleInput.value;

    // Doctor specific contexts
    let specSelect = document.querySelectorAll('input[name="specialties[]"]:checked');
    if (specSelect.length > 0) {
        context.specialties = Array.from(specSelect).map(opt => opt.parentNode.innerText.trim()).join(', ');
    }
    
    let designationBox = document.querySelector('input[name="designation"]');
    if (designationBox) context.designation = designationBox.value;

    let qualBox = document.querySelector('input[name="qualifications"]');
    if (qualBox) context.qualifications = qualBox.value;

    let expBox = document.querySelector('input[name="experience_years"]');
    if (expBox) context.experience_years = expBox.value;

    let dobBox = document.querySelector('input[name="dob"]');
    if (dobBox) context.dob = dobBox.value;

    // Chambers / Hospitals & Districts
    let chamberNames = [];
    document.querySelectorAll('input[name^="chambers["]').forEach(inp => {
        if (inp.name.endsWith('[name]') && inp.value.trim()) {
            chamberNames.push(inp.value.trim());
        }
    });
    let districtNames = [];
    document.querySelectorAll('select[name^="chambers["]').forEach(sel => {
        if (sel.name.endsWith('[district_id]') && sel.options[sel.selectedIndex] && sel.options[sel.selectedIndex].value) {
            let txt = sel.options[sel.selectedIndex].text;
            if (txt && !txt.includes('--')) districtNames.push(txt);
        }
    });

    if (chamberNames.length > 0) {
        context.chambers = chamberNames.join(', ');
    }
    if (districtNames.length > 0) {
        context.districts = [...new Set(districtNames)].join(', ');
    }

    let bioBase = document.querySelector('textarea[name="bio_excerpt"]');
    if (bioBase) context.bio_excerpt = bioBase.value;

    // Hospital/Ambulance contexts
    let addrBox = document.querySelector('input[name="address"], textarea[name="address"]');
    if (addrBox) context.address = addrBox.value;

    let servicesInput = document.querySelector('input[name="services"]');
    if (servicesInput && servicesInput.value) {
        try {
            context.services = JSON.parse(servicesInput.value).join(', ');
        } catch(e) {}
    }

    let ambTypes = document.querySelectorAll('input[name="type[]"]:checked');
    if (ambTypes.length > 0) {
        context.ambulanceType = Array.from(ambTypes).map(opt => opt.parentNode.innerText.trim()).join(', ');
    }

    // fallback content for SEO from tinyMCE if available
    let tinymceContent = '';
    if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
        tinymceContent = tinymce.activeEditor.getContent({format: 'text'});
    } else {
        let textareas = document.querySelectorAll('textarea');
        textareas.forEach(ta => {
            if (ta.name !== 'seo[description]') {
                tinymceContent += ta.value + ' ';
            }
        });
    }
    context.content = tinymceContent.substring(0, 1000); // Send first 1000 chars

    return context;
}

async function generateAiContent(promptType, targetSelector, btnElement) {
    const originalText = btnElement.innerHTML;
    btnElement.innerHTML = `⏳ Generating...`;
    btnElement.disabled = true;

    try {
        const response = await fetch('/admin/ai/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                prompt_type: promptType,
                context: getAiContext()
            })
        });

        const data = await response.json();

        if (data.success) {
            if (targetSelector.startsWith('tinymce:')) {
                // If it's a TinyMCE editor
                let editorId = targetSelector.split(':')[1];
                if (typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
                    tinymce.get(editorId).setContent(data.content);
                }
            } else {
                let target = null;
                try {
                    target = document.querySelector(targetSelector);
                } catch(e){}
                
                if (target) {
                    target.value = data.content;
                }
            }
            btnElement.innerHTML = `✅ Inserted`;
            setTimeout(() => btnElement.innerHTML = originalText, 2000);
            showInlinePopup(btnElement, 'Content generated successfully!', 'success');
        } else {
            let errorMsg = data.message || 'Unknown error occurred.';
            btnElement.innerHTML = `❌ Failed`;
            setTimeout(() => btnElement.innerHTML = originalText, 2000);
            showInlinePopup(btnElement, 'AI Error: ' + errorMsg, 'error');
        }
    } catch (e) {
        console.error(e);
        btnElement.innerHTML = `❌ Error`;
        setTimeout(() => btnElement.innerHTML = originalText, 2000);
        showInlinePopup(btnElement, 'Fetch Error: Could not connect to the server. Check console.', 'error');
    } finally {
        btnElement.disabled = false;
    }
}

function showInlinePopup(btnElement, message, type = 'error') {
    // Remove if already exists
    let existing = btnElement.parentNode.querySelector('.ai-inline-popup');
    if (existing) existing.remove();

    let popup = document.createElement('div');
    popup.className = `ai-inline-popup absolute z-[9999] top-[110%] right-0 p-2.5 text-[11px] leading-tight rounded-lg shadow-xl border ${
        type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700'
    }`;
    popup.style.width = 'max-content';
    popup.style.maxWidth = '260px';
    popup.style.whiteSpace = 'normal';
    popup.innerHTML = `<strong>${type === 'error' ? 'Oops!' : 'Success!'}</strong><br>${message}`;

    // Ensure parent can hold absolute element correctly
    if (window.getComputedStyle(btnElement.parentNode).position === 'static') {
        btnElement.parentNode.style.position = 'relative';
    }
    
    btnElement.parentNode.appendChild(popup);

    setTimeout(() => {
        if (popup.parentNode) {
            popup.style.transition = 'opacity 0.3s ease';
            popup.style.opacity = '0';
            setTimeout(() => popup.remove(), 300);
        }
    }, type === 'error' ? 6000 : 3000);
}
