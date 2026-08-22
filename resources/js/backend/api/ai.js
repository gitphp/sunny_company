import http from './http';

function csrfToken() {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
}

function firstError(data) {
    const bag = data?.errors ?? {};
    return Object.values(bag)[0]?.[0] || data?.message || '';
}

export function fetchAiProviders(params) {
    return http.get('/ai/providers', { params });
}

export function fetchAiProviderOptions() {
    return http.get('/ai/providers/options');
}

export function createAiProvider(payload) {
    return http.post('/ai/providers', payload);
}

export function updateAiProvider(id, payload) {
    return http.put(`/ai/providers/${id}`, payload);
}

export function deleteAiProvider(id) {
    return http.delete(`/ai/providers/${id}`);
}

export function changeAiProviderStatus(id, status) {
    return http.put(`/ai/providers/${id}/status`, { status });
}

export function setDefaultAiProvider(id) {
    return http.put(`/ai/providers/${id}/default`);
}

export function testAiProvider(id) {
    return http.post(`/ai/providers/${id}/test`);
}

export async function streamChat(payload, onDelta, signal) {
    const response = await fetch('/api/admin/ai/chat', {
        method: 'POST',
        credentials: 'include',
        signal,
        headers: {
            'Content-Type': 'application/json',
            Accept: 'text/event-stream',
            'X-Requested-With': 'XMLHttpRequest',
            'X-XSRF-TOKEN': csrfToken(),
        },
        body: JSON.stringify({ ...payload, stream: true }),
    });

    if (!response.ok) {
        const data = await response.json().catch(() => ({}));
        throw new Error(firstError(data) || '对话失败');
    }

    const type = response.headers.get('content-type') || '';
    if (type.includes('application/json')) {
        const data = await response.json();
        if (data.content) {
            onDelta(data.content);
        }
        return;
    }

    const reader = response.body.getReader();
    const decoder = new TextDecoder();
    let buffer = '';

    while (true) {
        const { done, value } = await reader.read();
        if (done) {
            break;
        }
        buffer += decoder.decode(value, { stream: true });
        const chunks = buffer.split('\n\n');
        buffer = chunks.pop() ?? '';

        for (const chunk of chunks) {
            const line = chunk.split('\n').find((item) => item.startsWith('data:')) ?? '';
            const data = line.replace(/^data:\s*/, '').trim();
            if (!data || data === '[DONE]') {
                continue;
            }
            let parsed;
            try {
                parsed = JSON.parse(data);
            } catch {
                continue;
            }
            if (parsed.error) {
                throw new Error(parsed.error);
            }
            const text = parsed.choices?.[0]?.delta?.content || parsed.content || '';
            if (text) {
                onDelta(text);
            }
        }
    }
}
